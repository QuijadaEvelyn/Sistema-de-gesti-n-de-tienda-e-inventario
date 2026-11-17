<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Bitacora;
use App\Models\DetallePedido;
use App\Models\Devolucion;
use App\Models\Existencia;
use App\Models\MovimientoInventario;
use App\Models\Pedido;
use App\Models\PiezaUnica;
use App\Models\Sucursal;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Servicio encargado de gestionar el proceso completo de devolución y garantías
 * por defectos en ARCHIVUM:
 *  - Solicitud por parte del cliente con motivo, comentarios y fotografías de evidencia.
 *  - Revisión administrativa (Aprobación con generación de guía de retorno o Rechazo).
 *  - Recepción física en bóveda con decisión de reintegración al stock (con nueva graduación)
 *    o envío a mermas / baja de inventario y procesamiento de reembolso.
 */
class DevolucionService
{
    /**
     * Registra una solicitud de devolución o garantía enviada por el cliente desde su historial de pedidos.
     *
     * @param  array<int, UploadedFile|string|array<string, mixed>>  $archivosEvidencia
     */
    public function solicitarDevolucionCliente(
        Pedido $pedido,
        int $detallePedidoId,
        int $cantidad,
        string $motivo,
        ?string $comentariosCliente = null,
        array $archivosEvidencia = []
    ): Devolucion {
        if ($cantidad <= 0) {
            throw new RuntimeException('La cantidad solicitada para devolución debe ser mayor a cero.');
        }

        return DB::transaction(function () use (
            $pedido,
            $detallePedidoId,
            $cantidad,
            $motivo,
            $comentariosCliente,
            $archivosEvidencia
        ): Devolucion {
            /** @var Pedido $pedidoBloqueado */
            $pedidoBloqueado = Pedido::query()->lockForUpdate()->findOrFail($pedido->id);

            if (! in_array($pedidoBloqueado->estatus, ['pagado', 'enviado', 'entregado'], true)) {
                throw new RuntimeException(
                    "No es posible solicitar devolución para el pedido {$pedidoBloqueado->folio} con estatus '{$pedidoBloqueado->estatus}'."
                );
            }

            /** @var DetallePedido $detalle */
            $detalle = DetallePedido::query()
                ->where('id', $detallePedidoId)
                ->where('pedido_id', $pedidoBloqueado->id)
                ->lockForUpdate()
                ->firstOrFail();

            $cantidadYaEnTramite = (int) Devolucion::query()
                ->where('detalle_pedido_id', $detalle->id)
                ->whereIn('estatus', ['solicitada', 'aprobada', 'completada'])
                ->sum('cantidad');

            $disponibleParaDevolver = (int) $detalle->cantidad - $cantidadYaEnTramite;

            if ($cantidad > $disponibleParaDevolver) {
                throw new RuntimeException(
                    "La cantidad solicitada ({$cantidad}) supera las unidades disponibles para devolución ({$disponibleParaDevolver} u.) de esta pieza."
                );
            }

            $evidenciasGuardadas = [];
            foreach ($archivosEvidencia as $archivo) {
                if ($archivo instanceof UploadedFile) {
                    $ruta = (string) $archivo->store('devoluciones', 'public');
                    $rutaLimpia = ltrim(str_replace('\\', '/', $ruta), '/');
                    $evidenciasGuardadas[] = [
                        'nombre' => $archivo->getClientOriginalName(),
                        'ruta' => $rutaLimpia,
                        'url' => asset('storage/'.$rutaLimpia),
                        'mime' => $archivo->getClientMimeType() ?: 'image/jpeg',
                    ];
                } elseif (is_string($archivo) && trim($archivo) !== '') {
                    $rutaLimpia = ltrim(str_replace('\\', '/', trim($archivo)), '/');
                    $rutaLimpia = (string) preg_replace('#^(public/|storage/)#', '', $rutaLimpia);
                    $urlPublica = (str_starts_with($archivo, 'http://') || str_starts_with($archivo, 'https://'))
                        ? trim($archivo)
                        : asset('storage/'.$rutaLimpia);

                    $evidenciasGuardadas[] = [
                        'nombre' => basename($rutaLimpia),
                        'ruta' => $rutaLimpia,
                        'url' => $urlPublica,
                        'mime' => 'image/jpeg',
                    ];
                } elseif (is_array($archivo) && ! empty($archivo['url'])) {
                    $evidenciasGuardadas[] = $archivo;
                }
            }

            $montoBruto = round((float) $detalle->precio_unitario * $cantidad, 2);
            $montoReembolso = $this->ajustarMontoReembolsoPorPromocion($pedidoBloqueado, $montoBruto);
            $folioDevolucion = 'DEV-ARC-'.now()->format('Ymd').'-'.strtoupper(Str::random(4));

            $devolucion = Devolucion::create([
                'folio_devolucion' => $folioDevolucion,
                'pedido_id' => $pedidoBloqueado->id,
                'cliente_id' => $pedidoBloqueado->cliente_id,
                'detalle_pedido_id' => $detalle->id,
                'cantidad' => $cantidad,
                'motivo' => $motivo,
                'comentarios_cliente' => $comentariosCliente,
                'evidencias' => $evidenciasGuardadas,
                'estatus' => 'solicitada',
                'etapa_devolucion' => 'solicitada',
                'monto_reembolso' => $montoReembolso,
                'stock_procesado' => false,
            ]);

            Bitacora::create([
                'usuario_id' => auth()->id(),
                'accion' => 'DEVOLUCION_SOLICITADA',
                'tabla_afectada' => 'devoluciones',
                'registro_id' => $devolucion->id,
                'detalle' => [
                    'folio_devolucion' => $folioDevolucion,
                    'pedido_folio' => $pedidoBloqueado->folio,
                    'detalle_pedido_id' => $detalle->id,
                    'cantidad' => $cantidad,
                    'motivo' => $motivo,
                    'evidencias_count' => count($evidenciasGuardadas),
                ],
                'ip' => request()->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);

            return $devolucion;
        });
    }

    /**
     * Permite al personal de Administración o Ventas autorizar o rechazar una solicitud
     * de devolución entrante, generando la guía e indicaciones de retorno cuando se aprueba.
     */
    public function dictaminarSolicitud(
        Devolucion $devolucion,
        string $decision,
        ?string $dictamenAdmin = null,
        ?string $guiaRetorno = null,
        ?string $instruccionesRetorno = null,
        ?int $usuarioId = null
    ): Devolucion {
        if (! in_array($decision, ['aprobar', 'rechazar'], true)) {
            throw new RuntimeException("Decisión de dictamen inválida: {$decision}.");
        }

        return DB::transaction(function () use (
            $devolucion,
            $decision,
            $dictamenAdmin,
            $guiaRetorno,
            $instruccionesRetorno,
            $usuarioId
        ): Devolucion {
            /** @var Devolucion $registro */
            $registro = Devolucion::query()->with('pedido')->lockForUpdate()->findOrFail($devolucion->id);

            $operadorId = $usuarioId ?? auth()->id();

            if ($decision === 'rechazar') {
                $registro->update([
                    'estatus' => 'rechazada',
                    'etapa_devolucion' => 'rechazada',
                    'dictamen_admin' => $dictamenAdmin ?: 'Solicitud rechazada tras revisión pericial de evidencia.',
                    'revisado_por' => $operadorId,
                ]);

                Bitacora::create([
                    'usuario_id' => $operadorId,
                    'accion' => 'DEVOLUCION_RECHAZADA',
                    'tabla_afectada' => 'devoluciones',
                    'registro_id' => $registro->id,
                    'detalle' => [
                        'folio_devolucion' => $registro->folio_devolucion,
                        'pedido_folio' => $registro->pedido?->folio,
                        'dictamen' => $registro->dictamen_admin,
                    ],
                    'ip' => request()->ip() ?? '127.0.0.1',
                    'created_at' => now(),
                ]);

                return $registro->fresh();
            }

            $guiaGenerada = trim((string) $guiaRetorno) !== ''
                ? trim((string) $guiaRetorno)
                : 'RET-ARC-'.now()->format('ymd').'-'.strtoupper(Str::random(5));

            $instrucciones = trim((string) $instruccionesRetorno) !== ''
                ? trim((string) $instruccionesRetorno)
                : "Coloque la pieza dentro de su empaque antiácido original, adhiera la etiqueta de guía {$guiaGenerada} en el exterior y entréguela en custodia ARCHIVUM o al transportista asignado.";

            $registro->update([
                'estatus' => 'aprobada',
                'etapa_devolucion' => 'aprobada',
                'dictamen_admin' => $dictamenAdmin ?: 'Evidencia validada favorablemente. Retorno de pieza autorizado.',
                'guia_retorno' => $guiaGenerada,
                'instrucciones_retorno' => $instrucciones,
                'revisado_por' => $operadorId,
                'aprobada_en' => now(),
            ]);

            Bitacora::create([
                'usuario_id' => $operadorId,
                'accion' => 'DEVOLUCION_APROBADA',
                'tabla_afectada' => 'devoluciones',
                'registro_id' => $registro->id,
                'detalle' => [
                    'folio_devolucion' => $registro->folio_devolucion,
                    'pedido_folio' => $registro->pedido?->folio,
                    'guia_retorno' => $guiaGenerada,
                ],
                'ip' => request()->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);

            return $registro->fresh();
        });
    }

    /**
     * Registra la recepción física de la pieza devuelta, ejecuta el ajuste automático de inventario
     * (reintegración al stock de la sucursal con posible nueva graduación, o envío a mermas/baja)
     * y actualiza el estado a Producto Retornado o Reembolso Procesado.
     */
    public function procesarRetornoFisicoYStock(
        Devolucion $devolucion,
        string $destinoInventario,
        ?int $sucursalDestinoId = null,
        ?string $nuevaGraduacion = null,
        ?string $dictamenAdmin = null,
        bool $procesarReembolso = true,
        ?int $usuarioId = null
    ): Devolucion {
        if (! in_array($destinoInventario, ['reintegrar_stock', 'merma_baja'], true)) {
            throw new RuntimeException("Destino de inventario inválido: {$destinoInventario}.");
        }

        return DB::transaction(function () use (
            $devolucion,
            $destinoInventario,
            $sucursalDestinoId,
            $nuevaGraduacion,
            $dictamenAdmin,
            $procesarReembolso,
            $usuarioId
        ): Devolucion {
            /** @var Devolucion $registro */
            $registro = Devolucion::query()
                ->with(['pedido.envios', 'detallePedido.varianteSku'])
                ->lockForUpdate()
                ->findOrFail($devolucion->id);

            if ($registro->estatus === 'rechazada') {
                throw new RuntimeException('No se puede procesar recepción física de una devolución rechazada.');
            }

            $operadorId = $usuarioId ?? auth()->id();
            $detalle = $registro->detallePedido;
            $varianteSku = $detalle?->varianteSku;

            if (! $detalle || ! $varianteSku) {
                throw new RuntimeException('La devolución no cuenta con una variante SKU válida asociada.');
            }

            // Resolver la sucursal donde se recibe físicamente el artículo
            $sucursalId = $sucursalDestinoId
                ?? $registro->pedido?->envios?->first()?->sucursal_id
                ?? Sucursal::query()->where('activa', true)->value('id');

            if (! $sucursalId) {
                throw new RuntimeException('No existe una sucursal activa para procesar el retorno físico.');
            }

            /** @var Existencia $existencia */
            $existencia = Existencia::query()
                ->where('variante_sku_id', $varianteSku->id)
                ->where('sucursal_id', $sucursalId)
                ->lockForUpdate()
                ->first();

            if (! $existencia) {
                $existencia = Existencia::create([
                    'variante_sku_id' => $varianteSku->id,
                    'sucursal_id' => $sucursalId,
                    'cantidad' => 0,
                    'stock_minimo' => 1,
                ]);
            }

            $graduacionLimpia = $nuevaGraduacion !== null && trim($nuevaGraduacion) !== ''
                ? trim($nuevaGraduacion)
                : null;

            if (! $registro->stock_procesado) {
                if ($destinoInventario === 'reintegrar_stock') {
                    // 1. La pieza es reparable/revendible: regresa al stock de la sucursal
                    $existencia->increment('cantidad', (int) $registro->cantidad);

                    if ($graduacionLimpia !== null) {
                        $varianteSku->update([
                            'descripcion' => $graduacionLimpia,
                        ]);
                    }

                    $piezaUnica = PiezaUnica::query()
                        ->where('existencia_id', $existencia->id)
                        ->whereIn('estado', ['vendida', 'reservada'])
                        ->lockForUpdate()
                        ->first();

                    if ($piezaUnica) {
                        $piezaUnica->update(['estado' => 'disponible']);
                    }

                    $notaGraduacion = $graduacionLimpia ? " (Nueva graduación: {$graduacionLimpia})" : '';

                    MovimientoInventario::create([
                        'existencia_id' => $existencia->id,
                        'usuario_id' => $operadorId,
                        'tipo' => 'devolucion',
                        'cantidad' => abs((int) $registro->cantidad),
                        'motivo' => "Reintegración a stock por devolución {$registro->folio_devolucion} · Pedido {$registro->pedido?->folio}{$notaGraduacion}.",
                        'referencia_type' => Devolucion::class,
                        'referencia_id' => $registro->id,
                        'created_at' => now(),
                    ]);
                } else {
                    // 2. La pieza presenta daño irreversible: se envía a mermas / baja de inventario
                    // No se suma al stock disponible para la venta y se registra la baja auditable en movimientos.
                    MovimientoInventario::create([
                        'existencia_id' => $existencia->id,
                        'usuario_id' => $operadorId,
                        'tipo' => 'ajuste',
                        'cantidad' => -abs((int) $registro->cantidad),
                        'motivo' => "Baja por merma / garantía de pieza defectuosa ({$registro->folio_devolucion}) · Pedido {$registro->pedido?->folio}. Motivo: {$registro->etiquetaMotivo()}.",
                        'referencia_type' => Devolucion::class,
                        'referencia_id' => $registro->id,
                        'created_at' => now(),
                    ]);
                }
            }

            $nuevaEtapa = $procesarReembolso ? 'reembolso_procesado' : 'producto_retornado';
            $guia = $registro->guia_retorno ?: ('RET-ARC-'.now()->format('ymd').'-'.strtoupper(Str::random(5)));

            $registro->update([
                'estatus' => 'completada',
                'etapa_devolucion' => $nuevaEtapa,
                'guia_retorno' => $guia,
                'destino_inventario' => $destinoInventario,
                'sucursal_destino_id' => $existencia->sucursal_id,
                'nueva_graduacion' => $graduacionLimpia ?? $registro->nueva_graduacion,
                'dictamen_admin' => $dictamenAdmin ?: $registro->dictamen_admin,
                'stock_procesado' => true,
                'revisado_por' => $operadorId,
                'aprobada_en' => $registro->aprobada_en ?? now(),
                'recibida_en' => $registro->recibida_en ?? now(),
                'reembolsada_en' => $procesarReembolso ? now() : $registro->reembolsada_en,
            ]);

            Bitacora::create([
                'usuario_id' => $operadorId,
                'accion' => $destinoInventario === 'reintegrar_stock'
                    ? 'DEVOLUCION_REINTEGRADA_STOCK'
                    : 'DEVOLUCION_ENVIADA_MERMA',
                'tabla_afectada' => 'devoluciones',
                'registro_id' => $registro->id,
                'detalle' => [
                    'folio_devolucion' => $registro->folio_devolucion,
                    'pedido_folio' => $registro->pedido?->folio,
                    'destino_inventario' => $destinoInventario,
                    'sucursal_destino_id' => $existencia->sucursal_id,
                    'nueva_graduacion' => $graduacionLimpia,
                    'etapa_devolucion' => $nuevaEtapa,
                    'monto_reembolso' => $registro->monto_reembolso,
                ],
                'ip' => request()->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);

            return $registro->fresh();
        });
    }

    /**
     * Procesa la devolucion parcial o total de articulos de un pedido (compatibilidad directa).
     *
     * @param  int  $pedidoId  ID del pedido sobre el que se aplica la devolucion.
     * @param  array<int, array{detalle_pedido_id: int, cantidad: int}>  $itemsDevolver
     * @param  string  $motivo  Descripcion del motivo de la devolucion.
     * @return array<int, Devolucion> Lista de registros de devolucion creados.
     *
     * @throws RuntimeException Si el pedido no es devolvible o las cantidades son invalidas.
     */
    public function procesarDevolucion(int $pedidoId, array $itemsDevolver, string $motivo): array
    {
        if (empty($itemsDevolver)) {
            throw new RuntimeException('La lista de items a devolver no puede estar vacia.');
        }

        return DB::transaction(function () use ($pedidoId, $itemsDevolver, $motivo): array {
            /** @var Pedido $pedido */
            $pedido = Pedido::query()->lockForUpdate()->findOrFail($pedidoId);

            // Solo se aceptan devoluciones de pedidos entregados, enviados o pagados.
            if (! in_array($pedido->estatus, ['pagado', 'enviado', 'entregado'], true)) {
                throw new RuntimeException(
                    "No se puede procesar una devolucion para el pedido {$pedido->folio} "
                    ."con estatus '{$pedido->estatus}'."
                );
            }

            $devolucionesCreadas = [];
            $montoTotalReembolso = 0.0;

            foreach ($itemsDevolver as $item) {
                $detallePedidoId = (int) $item['detalle_pedido_id'];
                $cantidadSolicitada = (int) $item['cantidad'];

                if ($cantidadSolicitada <= 0) {
                    throw new RuntimeException(
                        "La cantidad a devolver del detalle #{$detallePedidoId} debe ser mayor a cero."
                    );
                }

                /** @var DetallePedido $detalle */
                $detalle = DetallePedido::query()
                    ->where('id', $detallePedidoId)
                    ->where('pedido_id', $pedido->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Calcular cuanto se ha devuelto previamente (solo aprobadas o completadas).
                $cantidadPreviamenteDevuelta = (int) Devolucion::query()
                    ->where('detalle_pedido_id', $detalle->id)
                    ->whereIn('estatus', ['aprobada', 'completada'])
                    ->sum('cantidad');

                // La cantidad maxima devolvible = cantidad_pedida - ya_devuelta.
                $cantidadMaximaDevolvible = $detalle->cantidad - $cantidadPreviamenteDevuelta;

                if ($cantidadSolicitada > $cantidadMaximaDevolvible) {
                    throw new RuntimeException(
                        "La cantidad a devolver ({$cantidadSolicitada}) para el detalle #{$detalle->id} "
                        ."supera el maximo devolvible ({$cantidadMaximaDevolvible} u.). "
                        ."Pedidas: {$detalle->cantidad}, ya devueltas: {$cantidadPreviamenteDevuelta}."
                    );
                }

                // Calcular el monto bruto de reembolso y ajustar por promocion.
                $montoReembolso = round((float) $detalle->precio_unitario * $cantidadSolicitada, 2);
                $montoReembolso = $this->ajustarMontoReembolsoPorPromocion($pedido, $montoReembolso);

                // Registrar la devolucion.
                $devolucion = Devolucion::create([
                    'folio_devolucion' => 'DEV-ARC-'.now()->format('Ymd').'-'.strtoupper(Str::random(4)),
                    'pedido_id' => $pedido->id,
                    'cliente_id' => $pedido->cliente_id,
                    'detalle_pedido_id' => $detalle->id,
                    'cantidad' => $cantidadSolicitada,
                    'motivo' => $motivo,
                    'estatus' => 'aprobada',
                    'etapa_devolucion' => 'reembolso_procesado',
                    'destino_inventario' => 'reintegrar_stock',
                    'stock_procesado' => true,
                    'monto_reembolso' => $montoReembolso,
                ]);

                $devolucionesCreadas[] = $devolucion;
                $montoTotalReembolso += $montoReembolso;

                // Reintegrar el stock a la existencia de la variante.
                $existencia = Existencia::query()
                    ->where('variante_sku_id', $detalle->variante_sku_id)
                    ->lockForUpdate()
                    ->first();

                if ($existencia) {
                    $existencia->cantidad += $cantidadSolicitada;
                    $existencia->save();

                    MovimientoInventario::create([
                        'existencia_id' => $existencia->id,
                        'usuario_id' => auth()->id(),
                        'tipo' => 'devolucion',
                        'cantidad' => abs($cantidadSolicitada),
                        'motivo' => "Devolucion de {$cantidadSolicitada} u. - Pedido {$pedido->folio}. Motivo: {$motivo}.",
                        'referencia_type' => Devolucion::class,
                        'referencia_id' => $devolucion->id,
                        'created_at' => now(),
                    ]);
                }
            }

            Bitacora::create([
                'usuario_id' => auth()->id(),
                'accion' => 'DEVOLUCION_PROCESADA',
                'tabla_afectada' => 'devoluciones',
                'registro_id' => $pedido->id,
                'detalle' => [
                    'pedido_folio' => $pedido->folio,
                    'total_items_devueltos' => count($devolucionesCreadas),
                    'monto_total_reembolso' => $montoTotalReembolso,
                    'motivo' => $motivo,
                ],
                'ip' => request()->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);

            Log::info("[DevolucionService] Devolucion procesada para el pedido {$pedido->folio}.", [
                'pedido_id' => $pedido->id,
                'total_items_devueltos' => count($devolucionesCreadas),
                'monto_total_reembolso' => $montoTotalReembolso,
            ]);

            return $devolucionesCreadas;
        });
    }

    /**
     * Ajusta el monto de reembolso bruto distribuyendo el descuento de promocion
     * del pedido de forma proporcional a la parte devuelta.
     */
    private function ajustarMontoReembolsoPorPromocion(Pedido $pedido, float $montoReembolso): float
    {
        $descuentoPedido = (float) $pedido->descuento;
        $subtotalPedido = (float) $pedido->subtotal;

        if ($descuentoPedido <= 0 || $subtotalPedido <= 0) {
            return $montoReembolso;
        }

        $proporcionDescuento = $descuentoPedido / $subtotalPedido;
        $descuentoProporcional = round($montoReembolso * $proporcionDescuento, 2);

        return max(0.0, $montoReembolso - $descuentoProporcional);
    }
}
