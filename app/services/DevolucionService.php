<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Bitacora;
use App\Models\DetallePedido;
use App\Models\Devolucion;
use App\Models\Existencia;
use App\Models\MovimientoInventario;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Servicio encargado de gestionar el proceso completo de devolucion de mercancia.
 *
 * Valida que la cantidad a devolver no supere lo efectivamente entregado menos
 * las devoluciones previas aprobadas o completadas, reintegra el stock al
 * inventario y ajusta el monto de reembolso si el pedido tenia un descuento
 * de promocion aplicado.
 */
class DevolucionService
{
    /**
     * Procesa la devolucion parcial o total de articulos de un pedido.
     *
     * Flujo:
     *  1. Valida que el pedido exista y este en un estado devolvible.
     *  2. Por cada item a devolver, comprueba que la cantidad no supere
     *     `cantidad_pedida - cantidad_previamente_devuelta`.
     *  3. Registra la devolucion en la tabla `devoluciones`.
     *  4. Reintegra el stock a la existencia correspondiente.
     *  5. Registra el movimiento de inventario tipo "devolucion".
     *  6. Ajusta el monto de reembolso distribuyendo el descuento del pedido
     *     proporcionalmente para evitar reembolsar mas de lo pagado.
     *
     * @param  int  $pedidoId  ID del pedido sobre el que se aplica la devolucion.
     * @param  array<int, array{detalle_pedido_id: int, cantidad: int}>  $itemsDevolver
     *                                                                                   Items a devolver. Cada elemento contiene:
     *                                                                                   - `detalle_pedido_id`: ID de la linea de detalle del pedido.
     *                                                                                   - `cantidad`: Unidades a devolver.
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
                    'pedido_id' => $pedido->id,
                    'cliente_id' => $pedido->cliente_id,
                    'detalle_pedido_id' => $detalle->id,
                    'cantidad' => $cantidadSolicitada,
                    'motivo' => $motivo,
                    'estatus' => 'aprobada',
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

    // -------------------------------------------------------------------------
    // Helpers privados
    // -------------------------------------------------------------------------

    /**
     * Ajusta el monto de reembolso bruto distribuyendo el descuento de promocion
     * del pedido de forma proporcional a la parte devuelta.
     *
     * Evita reembolsar mas de lo que el cliente pago en realidad cuando el pedido
     * tenia un descuento de monto fijo o porcentual aplicado.
     *
     * @param  Pedido  $pedido  Pedido del que se origino la devolucion.
     * @param  float  $montoReembolso  Monto bruto calculado antes de descuentos.
     * @return float Monto neto de reembolso ajustado.
     */
    private function ajustarMontoReembolsoPorPromocion(Pedido $pedido, float $montoReembolso): float
    {
        $descuentoPedido = (float) $pedido->descuento;
        $subtotalPedido = (float) $pedido->subtotal;

        if ($descuentoPedido <= 0 || $subtotalPedido <= 0) {
            return $montoReembolso;
        }

        // Proporcion del descuento sobre el subtotal del pedido.
        $proporcionDescuento = $descuentoPedido / $subtotalPedido;

        // Descuento proporcional que corresponde a la parte que se devuelve.
        $descuentoProporcional = round($montoReembolso * $proporcionDescuento, 2);

        return max(0.0, $montoReembolso - $descuentoProporcional);
    }
}
