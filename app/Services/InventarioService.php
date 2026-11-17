<?php

namespace App\Services;

use App\Exceptions\StockInsuficienteException;
use App\Models\Bitacora;
use App\Models\Existencia;
use App\Models\MovimientoInventario;
use App\Models\PiezaUnica;
use App\Models\Sucursal;
use App\Models\Usuario;
use App\Models\VarianteSku;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InventarioService
{
    /**
     * Resuelve la sucursal activa objetivo o la predeterminada del sistema.
     *
     * @throws RuntimeException
     */
    public function resolverSucursal(?int $sucursalId = null): Sucursal
    {
        if ($sucursalId !== null) {
            $sucursal = Sucursal::query()->where('activa', true)->find($sucursalId);

            if (! $sucursal) {
                throw new RuntimeException("La sucursal con ID {$sucursalId} no existe o no está activa.");
            }

            return $sucursal;
        }

        $sucursalPredeterminada = Sucursal::query()->where('activa', true)->first();

        if (! $sucursalPredeterminada) {
            throw new RuntimeException('No existen sucursales activas registradas en el sistema para gestionar inventario.');
        }

        return $sucursalPredeterminada;
    }

    /**
     * Obtiene el registro de existencia para una variante en una sucursal, con opción de bloqueo pesimista.
     */
    public function obtenerExistencia(
        int|VarianteSku $variante,
        ?int $sucursalId = null,
        bool $bloquear = false
    ): Existencia {
        $varianteSkuId = $variante instanceof VarianteSku ? $variante->id : $variante;
        $sucursal = $this->resolverSucursal($sucursalId);

        $query = Existencia::query()
            ->where('variante_sku_id', $varianteSkuId)
            ->where('sucursal_id', $sucursal->id);

        if ($bloquear) {
            $query->lockForUpdate();
        }

        $existencia = $query->first();

        if (! $existencia) {
            $existencia = Existencia::create([
                'variante_sku_id' => $varianteSkuId,
                'sucursal_id' => $sucursal->id,
                'cantidad' => 0,
                'stock_minimo' => 0,
            ]);
        }

        return $existencia;
    }

    /**
     * Valida que exista stock suficiente para una variante en una sucursal dada.
     *
     * @throws StockInsuficienteException
     */
    public function verificarStock(
        int|VarianteSku $variante,
        int $cantidadRequerida,
        ?int $sucursalId = null,
        bool $bloquear = false
    ): Existencia {
        if ($cantidadRequerida <= 0) {
            throw new RuntimeException('La cantidad solicitada debe ser mayor a cero.');
        }

        $existencia = $this->obtenerExistencia($variante, $sucursalId, $bloquear);
        $varianteModel = $variante instanceof VarianteSku
            ? $variante
            : VarianteSku::with('producto')->find($variante);

        $identificadorSku = $varianteModel?->sku ?? "ID #{$existencia->variante_sku_id}";
        $nombreProducto = $varianteModel?->producto?->nombre ?? 'Producto';

        if ($existencia->cantidad < $cantidadRequerida) {
            throw new StockInsuficienteException(
                message: "Stock insuficiente para '{$nombreProducto}' (SKU: {$identificadorSku}). Solicitado: {$cantidadRequerida}, Disponible: {$existencia->cantidad}.",
                varianteSkuId: $existencia->variante_sku_id,
                solicitado: $cantidadRequerida,
                disponible: $existencia->cantidad,
            );
        }

        return $existencia;
    }

    /**
     * Descuenta existencias del inventario, registra el movimiento y deja rastro en bitácora.
     *
     * @throws StockInsuficienteException
     */
    public function descontarStock(
        int|VarianteSku $variante,
        int $cantidad,
        ?int $sucursalId = null,
        ?Usuario $usuario = null,
        ?Model $referencia = null,
        string $motivo = 'Salida de almacén por venta'
    ): MovimientoInventario {
        return DB::transaction(function () use ($variante, $cantidad, $sucursalId, $usuario, $referencia, $motivo): MovimientoInventario {
            $existencia = $this->verificarStock($variante, $cantidad, $sucursalId, bloquear: true);

            $existencia->cantidad -= $cantidad;
            $existencia->save();

            // Bloquear piezas únicas disponibles si existen para esta existencia y marcarlas como vendidas
            $piezas = PiezaUnica::query()
                ->where('existencia_id', $existencia->id)
                ->where('estado', 'disponible')
                ->lockForUpdate()
                ->limit($cantidad)
                ->get();

            foreach ($piezas as $pieza) {
                $pieza->update(['estado' => 'vendida']);
            }

            $movimiento = MovimientoInventario::create([
                'existencia_id' => $existencia->id,
                'usuario_id' => $usuario?->id ?? auth()->id(),
                'tipo' => 'salida',
                'cantidad' => -abs($cantidad),
                'motivo' => $motivo,
                'referencia_type' => $referencia ? $referencia::class : null,
                'referencia_id' => $referencia?->getKey(),
                'created_at' => now(),
            ]);

            Bitacora::create([
                'usuario_id' => $usuario?->id ?? auth()->id(),
                'accion' => 'STOCK_DESCONTADO',
                'tabla_afectada' => 'existencias',
                'registro_id' => $existencia->id,
                'detalle' => [
                    'variante_sku_id' => $existencia->variante_sku_id,
                    'sucursal_id' => $existencia->sucursal_id,
                    'cantidad_descontada' => $cantidad,
                    'stock_restante' => $existencia->cantidad,
                    'motivo' => $motivo,
                    'referencia_tipo' => $referencia ? class_basename($referencia) : null,
                    'referencia_id' => $referencia?->getKey(),
                    'piezas_unicas_afectadas' => $piezas->pluck('numero_serie')->toArray(),
                ],
                'ip' => request()->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);

            return $movimiento;
        });
    }

    /**
     * Incrementa existencias en el inventario (por entrada, devolución o restitución).
     */
    public function aumentarStock(
        int|VarianteSku $variante,
        int $cantidad,
        ?int $sucursalId = null,
        ?Usuario $usuario = null,
        ?Model $referencia = null,
        string $motivo = 'Entrada de mercancía a almacén',
        string $tipo = 'entrada'
    ): MovimientoInventario {
        if ($cantidad <= 0) {
            throw new RuntimeException('La cantidad a ingresar debe ser mayor a cero.');
        }

        return DB::transaction(function () use ($variante, $cantidad, $sucursalId, $usuario, $referencia, $motivo, $tipo): MovimientoInventario {
            $existencia = $this->obtenerExistencia($variante, $sucursalId, bloquear: true);
            $existencia->cantidad += $cantidad;
            $existencia->save();

            // Si es devolucion y hay piezas vendidas asociadas, restaurarlas a disponible con lock
            if ($tipo === 'devolucion') {
                $piezasDevueltas = PiezaUnica::query()
                    ->where('existencia_id', $existencia->id)
                    ->where('estado', 'vendida')
                    ->lockForUpdate()
                    ->limit($cantidad)
                    ->get();

                foreach ($piezasDevueltas as $pieza) {
                    $pieza->update(['estado' => 'disponible']);
                }
            }

            $movimiento = MovimientoInventario::create([
                'existencia_id' => $existencia->id,
                'usuario_id' => $usuario?->id ?? auth()->id(),
                'tipo' => $tipo,
                'cantidad' => abs($cantidad),
                'motivo' => $motivo,
                'referencia_type' => $referencia ? $referencia::class : null,
                'referencia_id' => $referencia?->getKey(),
                'created_at' => now(),
            ]);

            Bitacora::create([
                'usuario_id' => $usuario?->id ?? auth()->id(),
                'accion' => 'STOCK_AUMENTADO',
                'tabla_afectada' => 'existencias',
                'registro_id' => $existencia->id,
                'detalle' => [
                    'variante_sku_id' => $existencia->variante_sku_id,
                    'sucursal_id' => $existencia->sucursal_id,
                    'cantidad_ingresada' => $cantidad,
                    'stock_resultante' => $existencia->cantidad,
                    'motivo' => $motivo,
                    'tipo_movimiento' => $tipo,
                ],
                'ip' => request()->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);

            return $movimiento;
        });
    }
}
