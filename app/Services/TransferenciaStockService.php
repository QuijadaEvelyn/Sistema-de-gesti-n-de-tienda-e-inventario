<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Bitacora;
use App\Models\Existencia;
use App\Models\MovimientoInventario;
use App\Models\Sucursal;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Servicio responsable de gestionar el ciclo de vida de una transferencia de
 * inventario entre sucursales: envio desde el origen (mercancia en transito)
 * y recepcion en el destino (confirmacion de llegada).
 *
 * La tabla `movimientos_inventario` actua como libro contable:
 *   - Al registrar el envio  -> salida tipo "traspaso" en la sucursal origen.
 *   - Al confirmar la recepcion -> entrada tipo "traspaso" en la sucursal destino.
 *
 * No existe una tabla dedicada de "transferencias" en el esquema actual,
 * por lo que se usan movimientos de tipo "traspaso" con referencia cruzada
 * (referencia_type = Sucursal::class, referencia_id = ID sucursal destino)
 * para mantener trazabilidad sin duplicar existencias durante el transito.
 */
class TransferenciaStockService
{
    /**
     * Registra el envio de stock desde una sucursal origen hacia una destino.
     *
     * Descuenta las cantidades de la sucursal origen y crea un movimiento de
     * tipo "traspaso" (salida) por cada item. El stock NO se anade a la
     * sucursal destino en este momento: permanece "en transito" hasta que
     * se llame a `confirmarRecepcionTransferencia`.
     *
     * @param  int  $sucursalOrigenId  ID de la sucursal que envia.
     * @param  int  $sucursalDestinoId  ID de la sucursal que recibira.
     * @param  array<int, array{variante_sku_id: int, cantidad: int}>  $items
     *                                                                         Lista de items a transferir. Cada elemento contiene:
     *                                                                         - `variante_sku_id`: ID de la variante a mover.
     *                                                                         - `cantidad`: Unidades a enviar.
     * @param  Usuario|null  $usuario  Usuario que registra el envio.
     * @return array<int, MovimientoInventario> Movimientos de salida creados (uno por item).
     *
     * @throws RuntimeException Si una sucursal no existe, no esta activa, o no hay stock.
     */
    public function registrarEnvioTransferencia(
        int $sucursalOrigenId,
        int $sucursalDestinoId,
        array $items,
        ?Usuario $usuario = null
    ): array {
        if (empty($items)) {
            throw new RuntimeException('La lista de items para la transferencia no puede estar vacia.');
        }

        if ($sucursalOrigenId === $sucursalDestinoId) {
            throw new RuntimeException('La sucursal de origen y destino no pueden ser la misma.');
        }

        return DB::transaction(function () use ($sucursalOrigenId, $sucursalDestinoId, $items, $usuario): array {
            $origen = $this->resolverSucursalActiva($sucursalOrigenId);
            $destino = $this->resolverSucursalActiva($sucursalDestinoId);

            $movimientosCreados = [];

            foreach ($items as $item) {
                $varianteSkuId = (int) $item['variante_sku_id'];
                $cantidad = (int) $item['cantidad'];

                if ($cantidad <= 0) {
                    throw new RuntimeException(
                        "La cantidad para la variante SKU {$varianteSkuId} debe ser mayor a cero."
                    );
                }

                /** @var Existencia $existenciaOrigen */
                $existenciaOrigen = Existencia::query()
                    ->where('variante_sku_id', $varianteSkuId)
                    ->where('sucursal_id', $origen->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($existenciaOrigen->cantidad < $cantidad) {
                    throw new RuntimeException(
                        "Stock insuficiente en la sucursal '{$origen->nombre}' para la variante SKU {$varianteSkuId}. "
                        ."Disponible: {$existenciaOrigen->cantidad}, Solicitado: {$cantidad}."
                    );
                }

                // Descontar de la sucursal origen.
                $existenciaOrigen->cantidad -= $cantidad;
                $existenciaOrigen->save();

                // Registrar movimiento de salida tipo "traspaso".
                $movimiento = MovimientoInventario::create([
                    'existencia_id' => $existenciaOrigen->id,
                    'usuario_id' => $usuario?->id ?? auth()->id(),
                    'tipo' => 'traspaso',
                    'cantidad' => -abs($cantidad),
                    'motivo' => "Envio de transferencia hacia sucursal '{$destino->nombre}' (ID {$destino->id}).",
                    'referencia_type' => Sucursal::class,
                    'referencia_id' => $destino->id,
                    'created_at' => now(),
                ]);

                $movimientosCreados[] = $movimiento;

                Bitacora::create([
                    'usuario_id' => $usuario?->id ?? auth()->id(),
                    'accion' => 'TRANSFERENCIA_ENVIADA',
                    'tabla_afectada' => 'movimientos_inventario',
                    'registro_id' => $movimiento->id,
                    'detalle' => [
                        'variante_sku_id' => $varianteSkuId,
                        'sucursal_origen' => $origen->nombre,
                        'sucursal_destino' => $destino->nombre,
                        'cantidad_enviada' => $cantidad,
                        'stock_restante' => $existenciaOrigen->cantidad,
                    ],
                    'ip' => request()->ip() ?? '127.0.0.1',
                    'created_at' => now(),
                ]);
            }

            Log::info('[TransferenciaStockService] Envio de transferencia registrado.', [
                'origen_id' => $sucursalOrigenId,
                'destino_id' => $sucursalDestinoId,
                'total_items' => count($movimientosCreados),
            ]);

            return $movimientosCreados;
        });
    }

    /**
     * Confirma la recepcion de una transferencia en la sucursal destino.
     *
     * Toma el movimiento de salida tipo "traspaso" identificado por su ID,
     * suma las cantidades a las existencias de la sucursal destino y registra
     * el movimiento de entrada correspondiente.
     *
     * @param  int  $transferenciaId  ID del movimiento de salida creado por
     *                                `registrarEnvioTransferencia`.
     * @param  int  $usuarioId  ID del usuario que confirma la recepcion.
     * @return array<int, MovimientoInventario> Movimientos de entrada creados.
     *
     * @throws RuntimeException Si el movimiento de origen no existe o no es un traspaso.
     */
    public function confirmarRecepcionTransferencia(int $transferenciaId, int $usuarioId): array
    {
        return DB::transaction(function () use ($transferenciaId, $usuarioId): array {
            /** @var MovimientoInventario $movimientoSalida */
            $movimientoSalida = MovimientoInventario::query()
                ->where('id', $transferenciaId)
                ->where('tipo', 'traspaso')
                ->lockForUpdate()
                ->firstOrFail();

            $sucursalDestinoId = (int) $movimientoSalida->referencia_id;
            $cantidadRecibida = abs($movimientoSalida->cantidad);

            // Obtener la variante SKU de la existencia de origen.
            $existenciaOrigen = Existencia::query()->findOrFail($movimientoSalida->existencia_id);
            $varianteSkuId = $existenciaOrigen->variante_sku_id;

            // Buscar o crear el registro de existencia en la sucursal destino.
            $existenciaDestino = Existencia::query()
                ->where('variante_sku_id', $varianteSkuId)
                ->where('sucursal_id', $sucursalDestinoId)
                ->lockForUpdate()
                ->first();

            if (! $existenciaDestino) {
                $existenciaDestino = Existencia::create([
                    'variante_sku_id' => $varianteSkuId,
                    'sucursal_id' => $sucursalDestinoId,
                    'cantidad' => 0,
                    'stock_minimo' => 0,
                ]);
            }

            // Sumar la mercancia recibida en la sucursal destino.
            $existenciaDestino->cantidad += $cantidadRecibida;
            $existenciaDestino->save();

            // Registrar el movimiento de entrada tipo "traspaso" en el destino.
            $movimientoEntrada = MovimientoInventario::create([
                'existencia_id' => $existenciaDestino->id,
                'usuario_id' => $usuarioId,
                'tipo' => 'traspaso',
                'cantidad' => abs($cantidadRecibida),
                'motivo' => "Recepcion de transferencia confirmada (movimiento de salida #{$transferenciaId}).",
                'referencia_type' => MovimientoInventario::class,
                'referencia_id' => $transferenciaId,
                'created_at' => now(),
            ]);

            Bitacora::create([
                'usuario_id' => $usuarioId,
                'accion' => 'TRANSFERENCIA_RECIBIDA',
                'tabla_afectada' => 'movimientos_inventario',
                'registro_id' => $movimientoEntrada->id,
                'detalle' => [
                    'variante_sku_id' => $varianteSkuId,
                    'sucursal_destino_id' => $sucursalDestinoId,
                    'cantidad_recibida' => $cantidadRecibida,
                    'stock_resultante' => $existenciaDestino->cantidad,
                    'movimiento_salida_id' => $transferenciaId,
                ],
                'ip' => request()->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);

            Log::info('[TransferenciaStockService] Recepcion de transferencia confirmada.', [
                'movimiento_salida_id' => $transferenciaId,
                'movimiento_entrada_id' => $movimientoEntrada->id,
                'sucursal_destino_id' => $sucursalDestinoId,
                'cantidad_recibida' => $cantidadRecibida,
            ]);

            return [$movimientoEntrada];
        });
    }

    // -------------------------------------------------------------------------
    // Helpers privados
    // -------------------------------------------------------------------------

    /**
     * Resuelve una sucursal activa por ID o lanza una excepcion si no existe o esta inactiva.
     *
     * @throws RuntimeException
     */
    private function resolverSucursalActiva(int $sucursalId): Sucursal
    {
        $sucursal = Sucursal::query()
            ->where('id', $sucursalId)
            ->where('activa', true)
            ->first();

        if (! $sucursal) {
            throw new RuntimeException(
                "La sucursal con ID {$sucursalId} no existe o no esta activa."
            );
        }

        return $sucursal;
    }
}
