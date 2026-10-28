<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Bitacora;
use App\Models\MovimientoInventario;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class OrdenCompraProveedorService
{
    public function __construct(
        private readonly InventarioService $inventarioService,
        private readonly PreventaService $preventaService,
        private readonly NotificacionService $notificacionService,
    ) {}

    /**
     * Recepciona una remesa de stock entregada por un proveedor y suma las existencias al almacen.
     *
     * @param  array<int, array{variante_sku_id: int, cantidad: int}>  $items
     * @return array<int, MovimientoInventario>
     *
     * @throws RuntimeException
     */
    public function recepcionarStock(
        array $items,
        ?int $sucursalId = null,
        string $motivo = 'Recepcion de mercancia de proveedor',
        ?Usuario $usuario = null,
    ): array {
        if (empty($items)) {
            throw new RuntimeException('La lista de articulos a recepcionar no puede estar vacia.');
        }

        return DB::transaction(function () use ($items, $sucursalId, $motivo, $usuario): array {
            $sucursal = $this->inventarioService->resolverSucursal($sucursalId);
            $movimientos = [];

            foreach ($items as $item) {
                $varianteSkuId = (int) $item['variante_sku_id'];
                $cantidad = (int) $item['cantidad'];

                if ($cantidad <= 0) {
                    throw new RuntimeException("La cantidad para la variante SKU {$varianteSkuId} debe ser mayor a cero.");
                }

                $movimiento = $this->inventarioService->aumentarStock(
                    variante: $varianteSkuId,
                    cantidad: $cantidad,
                    sucursalId: $sucursal->id,
                    usuario: $usuario,
                    referencia: null,
                    motivo: $motivo,
                    tipo: 'entrada',
                );

                $movimientos[] = $movimiento;
            }

            Bitacora::create([
                'usuario_id' => $usuario?->id ?? auth()->id(),
                'accion' => 'STOCK_PROVEEDOR_RECEPCIONADO',
                'tabla_afectada' => 'movimientos_inventario',
                'registro_id' => $movimientos[0]->id ?? null,
                'detalle' => [
                    'sucursal_id' => $sucursal->id,
                    'total_items' => count($items),
                    'motivo' => $motivo,
                ],
                'ip' => request()->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);

            Log::info('[OrdenCompraProveedorService] Mercancia recepcionada con exito.', [
                'sucursal_id' => $sucursal->id,
                'total_movimientos' => count($movimientos),
            ]);

            return $movimientos;
        });
    }

    /**
     * Recepciona el stock de una orden de compra de proveedor y automaticamente surte
     * los pedidos en preventa pendientes asociados a esos productos.
     *
     * @param  array<int, array{variante_sku_id: int, cantidad: int}>  $items
     * @return array{movimientos: array<int, MovimientoInventario>, resumen_surtido: array{surtidos: int, pedidos_completados: int}}
     *
     * @throws RuntimeException
     */
    public function recepcionarYSurtirPreventas(
        int $ordenCompraId,
        array $items,
        ?int $sucursalId = null,
        ?Usuario $usuario = null,
    ): array {
        return DB::transaction(function () use ($ordenCompraId, $items, $sucursalId, $usuario): array {
            $motivo = "Recepcion orden de compra #{$ordenCompraId}";

            // 1. Ingresar el stock a existencias
            $movimientos = $this->recepcionarStock($items, $sucursalId, $motivo, $usuario);

            // 2. Surtir preventas pendientes en cola FIFO
            $resumenSurtido = $this->preventaService->vincularOrdenCompraAPreventas($ordenCompraId);

            Log::info("[OrdenCompraProveedorService] Orden de compra #{$ordenCompraId} procesada y preventas surtidas.", [
                'orden_compra_id' => $ordenCompraId,
                'pedidos_completados' => $resumenSurtido['pedidos_completados'],
                'detalles_surtidos' => $resumenSurtido['surtidos'],
            ]);

            return [
                'movimientos' => $movimientos,
                'resumen_surtido' => $resumenSurtido,
            ];
        });
    }
}
