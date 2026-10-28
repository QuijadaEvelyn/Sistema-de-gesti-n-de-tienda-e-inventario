<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Existencia;
use App\Models\PiezaUnica;
use App\Models\Sucursal;
use Illuminate\Support\Collection;

class ReporteInventarioService
{
    /**
     * Consulta la valoracion economica del inventario agrupada por sucursal.
     * Calcula el valor de cada existencia considerando el precio de la variante o, en su defecto,
     * el precio base del producto.
     *
     * @return array<int, array{sucursal_id: int, sucursal_nombre: string, total_skus: int, total_unidades: int, valor_total_inventario: float}>
     */
    public function valoracionPorSucursal(?int $sucursalId = null): array
    {
        $query = Sucursal::query()->where('activa', true);

        if ($sucursalId !== null) {
            $query->where('id', $sucursalId);
        }

        $sucursales = $query->with(['existencias.varianteSku.producto'])->get();

        $reporte = [];

        foreach ($sucursales as $sucursal) {
            $totalSkus = 0;
            $totalUnidades = 0;
            $valorTotal = 0.0;

            foreach ($sucursal->existencias as $existencia) {
                $variante = $existencia->varianteSku;
                if (! $variante) {
                    continue;
                }

                $precioUnitario = (float) ($variante->precio ?? $variante->producto?->precio ?? 0.0);
                $unidades = (int) $existencia->cantidad;

                $totalSkus++;
                $totalUnidades += $unidades;
                $valorTotal += ($unidades * $precioUnitario);
            }

            $reporte[] = [
                'sucursal_id' => $sucursal->id,
                'sucursal_nombre' => $sucursal->nombre,
                'total_skus' => $totalSkus,
                'total_unidades' => $totalUnidades,
                'valor_total_inventario' => round($valorTotal, 2),
            ];
        }

        return $reporte;
    }

    /**
     * Genera un reporte de rotacion y estado de piezas unicas (ejemplares numerados / certificados).
     *
     * @return array{total_piezas: int, total_disponibles: int, total_reservadas: int, total_vendidas: int, tasa_rotacion_porcentaje: float, detalle_por_existencia: array<int, array{existencia_id: int, sku: string, producto: string, disponibles: int, reservadas: int, vendidas: int}>}
     */
    public function rotacionPiezasUnicas(?int $sucursalId = null): array
    {
        $query = PiezaUnica::query()
            ->with(['existencia.varianteSku.producto', 'existencia.sucursal']);

        if ($sucursalId !== null) {
            $query->whereHas('existencia', function ($q) use ($sucursalId): void {
                $q->where('sucursal_id', $sucursalId);
            });
        }

        $piezas = $query->get();

        $totalDisponibles = 0;
        $totalReservadas = 0;
        $totalVendidas = 0;
        $porExistencia = [];

        foreach ($piezas as $pieza) {
            $estado = $pieza->estado;
            if ($estado === 'disponible') {
                $totalDisponibles++;
            } elseif ($estado === 'reservada') {
                $totalReservadas++;
            } elseif ($estado === 'vendida') {
                $totalVendidas++;
            }

            $existenciaId = $pieza->existencia_id;
            if (! isset($porExistencia[$existenciaId])) {
                $porExistencia[$existenciaId] = [
                    'existencia_id' => $existenciaId,
                    'sku' => $pieza->existencia?->varianteSku?->sku ?? 'N/A',
                    'producto' => $pieza->existencia?->varianteSku?->producto?->nombre ?? 'N/A',
                    'sucursal' => $pieza->existencia?->sucursal?->nombre ?? 'N/A',
                    'disponibles' => 0,
                    'reservadas' => 0,
                    'vendidas' => 0,
                ];
            }

            if ($estado === 'disponible') {
                $porExistencia[$existenciaId]['disponibles']++;
            } elseif ($estado === 'reservada') {
                $porExistencia[$existenciaId]['reservadas']++;
            } elseif ($estado === 'vendida') {
                $porExistencia[$existenciaId]['vendidas']++;
            }
        }

        $totalPiezas = $totalDisponibles + $totalReservadas + $totalVendidas;
        $tasaRotacion = $totalPiezas > 0
            ? round(($totalVendidas / $totalPiezas) * 100, 2)
            : 0.0;

        return [
            'total_piezas' => $totalPiezas,
            'total_disponibles' => $totalDisponibles,
            'total_reservadas' => $totalReservadas,
            'total_vendidas' => $totalVendidas,
            'tasa_rotacion_porcentaje' => $tasaRotacion,
            'detalle_por_existencia' => array_values($porExistencia),
        ];
    }

    /**
     * Consulta existencias de inventario que estan por debajo del stock minimo configurado.
     *
     * @return Collection<int, Existencia>
     */
    public function stockBajoMinimo(?int $sucursalId = null): Collection
    {
        $query = Existencia::query()
            ->whereColumn('cantidad', '<', 'stock_minimo')
            ->where('stock_minimo', '>', 0)
            ->with(['varianteSku.producto', 'sucursal']);

        if ($sucursalId !== null) {
            $query->where('sucursal_id', $sucursalId);
        }

        return $query->get();
    }
}
