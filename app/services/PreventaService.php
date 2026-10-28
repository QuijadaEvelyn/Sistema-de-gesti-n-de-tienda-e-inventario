<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Existencia;
use App\Models\MovimientoInventario;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class PreventaService
{
    /**
     * Limite maximo de unidades permitidas por preventa cuando el producto no
     * tiene configurado un `limite_preventa` explicito.
     */
    public const LIMITE_PREVENTA_DEFECTO = 5;

    /**
     * Valida que la cantidad solicitada no supere el limite de preventa configurado
     * para un producto o su variante SKU.
     *
     * Se busca el campo `limite_preventa` en el modelo Producto y, si no existe,
     * se usa la constante de fallback.
     *
     * @param  int  $productoId  ID del Producto base.
     * @param  int  $cantidad  Unidades que el cliente desea reservar en preventa.
     *
     * @throws RuntimeException Si la cantidad excede el cupo permitido.
     */
    public function validarCupoPreventa(int $productoId, int $cantidad): void
    {
        if ($cantidad <= 0) {
            throw new RuntimeException('La cantidad para preventa debe ser mayor a cero.');
        }

        /** @var Producto $producto */
        $producto = Producto::query()->findOrFail($productoId);

        // El limite puede estar en el producto o en la variante; se toma el menor definido.
        $limitePreventa = $this->resolverLimitePreventa($producto);

        if ($cantidad > $limitePreventa) {
            throw new RuntimeException(
                "La cantidad solicitada ({$cantidad}) supera el limite de preventa "
                ."permitido ({$limitePreventa}) para el producto '{$producto->nombre}'."
            );
        }

        Log::info('[PreventaService] Cupo de preventa validado.', [
            'producto_id' => $productoId,
            'cantidad' => $cantidad,
            'limite_preventa' => $limitePreventa,
        ]);
    }

    /**
     * Distribuye el stock recibido de un proveedor (a traves de una orden de compra)
     * a los pedidos de preventa que estan en estatus "pendiente", siguiendo el orden
     * de antiguedad (FIFO: primero en entrar, primero en ser surtido).
     *
     * Por cada detalle de pedido pendiente se descuenta la existencia disponible y
     * se actualiza el estatus del pedido a "pagado" cuando todas sus lineas han
     * sido surtidas.
     *
     * @param  int  $ordenCompraId  ID de la orden de compra cuyo stock ha llegado.
     *                              (Usado como referencia en movimientos_inventario.)
     * @return array{surtidos: int, pedidos_completados: int} Resumen del surtido.
     *
     * @throws RuntimeException Si no existen pedidos de preventa pendientes.
     */
    public function vincularOrdenCompraAPreventas(int $ordenCompraId): array
    {
        return DB::transaction(function () use ($ordenCompraId): array {
            /**
             * Pedidos en preventa pendientes, ordenados de mas antiguo a mas nuevo.
             *
             * @var Collection<int, Pedido> $pedidosPendientes
             */
            $pedidosPendientes = Pedido::query()
                ->where('estatus', 'pendiente')
                ->orderBy('created_at')
                ->lockForUpdate()
                ->get();

            if ($pedidosPendientes->isEmpty()) {
                throw new RuntimeException('No existen pedidos de preventa pendientes de surtir.');
            }

            $detallesSurtidos = 0;
            $pedidosCompletados = 0;

            foreach ($pedidosPendientes as $pedido) {
                $detalles = $pedido->detalles()->with('varianteSku')->get();
                $todasSurtidas = true;

                foreach ($detalles as $detalle) {
                    /** @var Existencia|null $existencia */
                    $existencia = Existencia::query()
                        ->where('variante_sku_id', $detalle->variante_sku_id)
                        ->lockForUpdate()
                        ->first();

                    // Si no hay existencia registrada o no hay stock suficiente, saltar.
                    if (! $existencia || $existencia->cantidad < $detalle->cantidad) {
                        $todasSurtidas = false;

                        continue;
                    }

                    // Descontar el stock asignado a este pedido de preventa.
                    $existencia->cantidad -= $detalle->cantidad;
                    $existencia->save();

                    // Registrar el movimiento de salida hacia el pedido de preventa.
                    MovimientoInventario::create([
                        'existencia_id' => $existencia->id,
                        'usuario_id' => auth()->id(),
                        'tipo' => 'salida',
                        'cantidad' => -abs($detalle->cantidad),
                        'motivo' => "Surtido de preventa desde orden de compra #{$ordenCompraId} - Pedido {$pedido->folio}.",
                        'referencia_type' => Pedido::class,
                        'referencia_id' => $pedido->id,
                        'created_at' => now(),
                    ]);

                    $detallesSurtidos++;
                }

                // Cuando todas las lineas del pedido fueron surtidas, cambiar estatus.
                if ($todasSurtidas) {
                    $pedido->update(['estatus' => 'pagado']);
                    $pedidosCompletados++;
                }
            }

            Log::info('[PreventaService] Orden de compra vinculada a preventas.', [
                'orden_compra_id' => $ordenCompraId,
                'detalles_surtidos' => $detallesSurtidos,
                'pedidos_completados' => $pedidosCompletados,
            ]);

            return [
                'surtidos' => $detallesSurtidos,
                'pedidos_completados' => $pedidosCompletados,
            ];
        });
    }

    // -------------------------------------------------------------------------
    // Helpers privados
    // -------------------------------------------------------------------------

    /**
     * Determina el limite de preventa aplicable para un producto.
     *
     * Intenta leer el atributo `limite_preventa` del producto; si no existe en
     * el modelo (campo futuro o virtual), usa la constante de fallback.
     */
    private function resolverLimitePreventa(Producto $producto): int
    {
        // Soporte opcional: si el modelo tiene el atributo (columna futura o virtual).
        if (isset($producto->limite_preventa) && $producto->limite_preventa > 0) {
            return (int) $producto->limite_preventa;
        }

        return self::LIMITE_PREVENTA_DEFECTO;
    }
}
