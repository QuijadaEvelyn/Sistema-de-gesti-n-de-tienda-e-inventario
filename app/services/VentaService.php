<?php

namespace App\Services;

use App\Models\Bitacora;
use App\Models\Carrito;
use App\Models\Cliente;
use App\Models\DetallePedido;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Usuario;
use App\Models\VarianteSku;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class VentaService
{
    /**
     * Inyecta el servicio de gestión de inventario.
     */
    public function __construct(
        protected InventarioService $inventarioService,
    ) {}

    /**
     * Procesa la compra de los artículos contenidos en el carrito de un cliente.
     * Todo el flujo se ejecuta dentro de una transacción atómica (DB::transaction).
     *
     * @param  array{metodo_pago?: string|null, direccion_envio_id?: int|null, sucursal_id?: int|null, descuento?: float}  $opciones
     *
     * @throws \Throwable
     */
    public function procesarVentaDesdeCarrito(
        Carrito $carrito,
        ?Usuario $usuario = null,
        array $opciones = []
    ): Pedido {
        return DB::transaction(function () use ($carrito, $usuario, $opciones): Pedido {
            $items = $carrito->items()->with(['varianteSku.producto'])->get();

            if ($items->isEmpty()) {
                throw new RuntimeException('No es posible procesar la venta porque el carrito de compras se encuentra vacío.');
            }

            $sucursal = $this->inventarioService->resolverSucursal($opciones['sucursal_id'] ?? null);

            // 1. Verificación previa de stock para todos los artículos (Fail-Fast)
            foreach ($items as $item) {
                $this->inventarioService->verificarStock(
                    variante: $item->varianteSku,
                    cantidadRequerida: $item->cantidad,
                    sucursalId: $sucursal->id,
                    bloquear: true
                );
            }

            // 2. Cálculo de importes
            $subtotal = 0.0;
            $lineasDetalle = [];

            foreach ($items as $item) {
                $variante = $item->varianteSku;
                $precioUnitario = (float) ($variante->precio ?? $variante->producto->precio);
                $subtotalLinea = $precioUnitario * $item->cantidad;
                $subtotal += $subtotalLinea;

                $lineasDetalle[] = [
                    'variante_sku_id' => $variante->id,
                    'cantidad' => $item->cantidad,
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $subtotalLinea,
                    'modelo_variante' => $variante,
                ];
            }

            $descuento = (float) ($opciones['descuento'] ?? 0.0);
            $total = max(0.0, $subtotal - $descuento);

            // 3. Creación del Pedido
            $pedido = Pedido::create([
                'folio' => $this->generarFolioUnico(),
                'cliente_id' => $carrito->cliente_id,
                'direccion_envio_id' => $opciones['direccion_envio_id'] ?? null,
                'estatus' => 'pagado',
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'total' => $total,
            ]);

            // 4. Inserción de líneas de detalle y descuento automático de stock
            foreach ($lineasDetalle as $linea) {
                DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'variante_sku_id' => $linea['variante_sku_id'],
                    'cantidad' => $linea['cantidad'],
                    'precio_unitario' => $linea['precio_unitario'],
                    'subtotal' => $linea['subtotal'],
                ]);

                // Descuento en la tabla de existencias y registro en movimientos_inventario
                $this->inventarioService->descontarStock(
                    variante: $linea['modelo_variante'],
                    cantidad: $linea['cantidad'],
                    sucursalId: $sucursal->id,
                    usuario: $usuario,
                    referencia: $pedido,
                    motivo: "Venta en tienda/online - Folio: {$pedido->folio}"
                );
            }

            // 5. Registro del pago si se especificó el método
            if (! empty($opciones['metodo_pago'])) {
                Pago::create([
                    'pedido_id' => $pedido->id,
                    'metodo' => $opciones['metodo_pago'],
                    'monto' => $total,
                    'estatus' => 'aprobado',
                    'pagado_en' => now(),
                ]);
            }

            // 6. Limpieza de los artículos del carrito de compras
            $carrito->items()->delete();

            // 7. Registro de la operación en Bitácora
            Bitacora::create([
                'usuario_id' => $usuario?->id ?? auth()->id(),
                'accion' => 'VENTA_PROCESADA',
                'tabla_afectada' => 'pedidos',
                'registro_id' => $pedido->id,
                'detalle' => [
                    'folio' => $pedido->folio,
                    'cliente_id' => $pedido->cliente_id,
                    'origen' => 'carrito_compras',
                    'sucursal_id' => $sucursal->id,
                    'articulos_cantidad' => count($lineasDetalle),
                    'total' => $total,
                ],
                'ip' => request()->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);

            return $pedido;
        });
    }

    /**
     * Procesa una venta directa en mostrador o punto de venta (POS) sin carrito previo.
     *
     * @param  list<array{variante_sku_id: int, cantidad: int, precio_unitario?: float|null}>  $items
     * @param  array{metodo_pago?: string|null, direccion_envio_id?: int|null, sucursal_id?: int|null, descuento?: float}  $opciones
     *
     * @throws \Throwable
     */
    public function procesarVentaDirecta(
        Cliente $cliente,
        array $items,
        ?Usuario $usuario = null,
        array $opciones = []
    ): Pedido {
        return DB::transaction(function () use ($cliente, $items, $usuario, $opciones): Pedido {
            if (empty($items)) {
                throw new RuntimeException('Debe incluir al menos un artículo para procesar la venta directa.');
            }

            $sucursal = $this->inventarioService->resolverSucursal($opciones['sucursal_id'] ?? null);

            // 1. Validar existencias de todos los items
            foreach ($items as $item) {
                $this->inventarioService->verificarStock(
                    variante: $item['variante_sku_id'],
                    cantidadRequerida: $item['cantidad'],
                    sucursalId: $sucursal->id,
                    bloquear: true
                );
            }

            // 2. Calcular montos
            $subtotal = 0.0;
            $detallesPreparados = [];

            foreach ($items as $item) {
                $variante = VarianteSku::with('producto')->findOrFail($item['variante_sku_id']);
                $precioUnitario = isset($item['precio_unitario'])
                    ? (float) $item['precio_unitario']
                    : (float) ($variante->precio ?? $variante->producto->precio);

                $subtotalLinea = $precioUnitario * $item['cantidad'];
                $subtotal += $subtotalLinea;

                $detallesPreparados[] = [
                    'variante_sku' => $variante,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $subtotalLinea,
                ];
            }

            $descuento = (float) ($opciones['descuento'] ?? 0.0);
            $total = max(0.0, $subtotal - $descuento);

            // 3. Crear pedido
            $pedido = Pedido::create([
                'folio' => $this->generarFolioUnico(),
                'cliente_id' => $cliente->id,
                'direccion_envio_id' => $opciones['direccion_envio_id'] ?? null,
                'estatus' => 'pagado',
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'total' => $total,
            ]);

            // 4. Insertar detalles y descontar existencias
            foreach ($detallesPreparados as $linea) {
                DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'variante_sku_id' => $linea['variante_sku']->id,
                    'cantidad' => $linea['cantidad'],
                    'precio_unitario' => $linea['precio_unitario'],
                    'subtotal' => $linea['subtotal'],
                ]);

                $this->inventarioService->descontarStock(
                    variante: $linea['variante_sku'],
                    cantidad: $linea['cantidad'],
                    sucursalId: $sucursal->id,
                    usuario: $usuario,
                    referencia: $pedido,
                    motivo: "Venta directa en mostrador - Folio: {$pedido->folio}"
                );
            }

            // 5. Registro de pago opcional
            if (! empty($opciones['metodo_pago'])) {
                Pago::create([
                    'pedido_id' => $pedido->id,
                    'metodo' => $opciones['metodo_pago'],
                    'monto' => $total,
                    'estatus' => 'aprobado',
                    'pagado_en' => now(),
                ]);
            }

            // 6. Auditoría en Bitácora
            Bitacora::create([
                'usuario_id' => $usuario?->id ?? auth()->id(),
                'accion' => 'VENTA_DIRECTA_PROCESADA',
                'tabla_afectada' => 'pedidos',
                'registro_id' => $pedido->id,
                'detalle' => [
                    'folio' => $pedido->folio,
                    'cliente_id' => $cliente->id,
                    'origen' => 'venta_directa_pos',
                    'sucursal_id' => $sucursal->id,
                    'articulos_cantidad' => count($detallesPreparados),
                    'total' => $total,
                ],
                'ip' => request()->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);

            return $pedido;
        });
    }

    /**
     * Confirma un pedido que se encontraba en estatus pendiente, aplicando el descuento de stock correspondiente.
     *
     * @throws \Throwable
     */
    public function confirmarPedido(
        Pedido $pedido,
        ?int $sucursalId = null,
        ?Usuario $usuario = null
    ): Pedido {
        return DB::transaction(function () use ($pedido, $sucursalId, $usuario): Pedido {
            if ($pedido->estatus === 'pagado') {
                return $pedido;
            }

            if ($pedido->estatus === 'cancelado') {
                throw new RuntimeException("No es posible confirmar el pedido {$pedido->folio} porque fue cancelado.");
            }

            $detalles = $pedido->detalles()->with('varianteSku')->get();
            $sucursal = $this->inventarioService->resolverSucursal($sucursalId);

            // Validar stock antes de confirmar
            foreach ($detalles as $detalle) {
                $this->inventarioService->verificarStock(
                    variante: $detalle->variante_sku_id,
                    cantidadRequerida: $detalle->cantidad,
                    sucursalId: $sucursal->id,
                    bloquear: true
                );
            }

            // Descontar inventario
            foreach ($detalles as $detalle) {
                $this->inventarioService->descontarStock(
                    variante: $detalle->variante_sku_id,
                    cantidad: $detalle->cantidad,
                    sucursalId: $sucursal->id,
                    usuario: $usuario,
                    referencia: $pedido,
                    motivo: "Confirmación de pedido pendiente - Folio: {$pedido->folio}"
                );
            }

            $pedido->update(['estatus' => 'pagado']);

            Bitacora::create([
                'usuario_id' => $usuario?->id ?? auth()->id(),
                'accion' => 'PEDIDO_CONFIRMADO',
                'tabla_afectada' => 'pedidos',
                'registro_id' => $pedido->id,
                'detalle' => [
                    'folio' => $pedido->folio,
                    'nuevo_estatus' => 'pagado',
                ],
                'ip' => request()->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);

            return $pedido;
        });
    }

    /**
     * Cancela un pedido y, si ya había descontado inventario (estatus 'pagado'), restituye las existencias al almacén.
     *
     * @throws \Throwable
     */
    public function cancelarPedido(
        Pedido $pedido,
        string $motivo = 'Cancelación solicitada por cliente o administrador',
        ?int $sucursalId = null,
        ?Usuario $usuario = null
    ): Pedido {
        return DB::transaction(function () use ($pedido, $motivo, $sucursalId, $usuario): Pedido {
            if ($pedido->estatus === 'cancelado') {
                return $pedido;
            }

            $estatusAnterior = $pedido->estatus;

            // Si el pedido ya estaba pagado, reingresamos el stock
            if ($estatusAnterior === 'pagado') {
                $detalles = $pedido->detalles()->get();
                $sucursal = $this->inventarioService->resolverSucursal($sucursalId);

                foreach ($detalles as $detalle) {
                    $this->inventarioService->aumentarStock(
                        variante: $detalle->variante_sku_id,
                        cantidad: $detalle->cantidad,
                        sucursalId: $sucursal->id,
                        usuario: $usuario,
                        referencia: $pedido,
                        motivo: "Reingreso por cancelación de pedido #{$pedido->folio}. Motivo: {$motivo}",
                        tipo: 'devolucion'
                    );
                }
            }

            $pedido->update(['estatus' => 'cancelado']);

            Bitacora::create([
                'usuario_id' => $usuario?->id ?? auth()->id(),
                'accion' => 'PEDIDO_CANCELADO',
                'tabla_afectada' => 'pedidos',
                'registro_id' => $pedido->id,
                'detalle' => [
                    'folio' => $pedido->folio,
                    'estatus_anterior' => $estatusAnterior,
                    'motivo' => $motivo,
                    'stock_restituido' => ($estatusAnterior === 'pagado'),
                ],
                'ip' => request()->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);

            return $pedido;
        });
    }

    /**
     * Genera un folio único y legible para el pedido.
     */
    protected function generarFolioUnico(): string
    {
        do {
            $folio = 'PED-'.date('Ymd').'-'.strtoupper(Str::random(6));
        } while (Pedido::query()->where('folio', $folio)->exists());

        return $folio;
    }
}
