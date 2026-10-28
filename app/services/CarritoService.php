<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\StockInsuficienteException;
use App\Models\Carrito;
use App\Models\CarritoItem;
use App\Models\Existencia;
use App\Models\Promocion;
use App\Models\ReservaStock;
use Illuminate\Support\Facades\DB;

class CarritoService
{
    public function __construct(
        private readonly InventarioService $inventarioService,
        private readonly ReservaService $reservaService,
    ) {}

    /**
     * Agrega un item al carrito, incrementando la cantidad si ya existe.
     *
     *
     * @throws StockInsuficienteException
     */
    public function agregarItem(
        Carrito $carrito,
        int $varianteSkuId,
        int $cantidad,
        ?int $sucursalId = null,
    ): CarritoItem {
        return DB::transaction(function () use ($carrito, $varianteSkuId, $cantidad, $sucursalId): CarritoItem {
            $existencia = $this->inventarioService->obtenerExistencia($varianteSkuId, $sucursalId, bloquear: true);

            $this->inventarioService->verificarStock($varianteSkuId, $cantidad, $sucursalId, bloquear: true);

            /** @var CarritoItem|null $item */
            $item = CarritoItem::where('carrito_id', $carrito->id)
                ->where('variante_sku_id', $varianteSkuId)
                ->first();

            if ($item !== null) {
                $item->cantidad += $cantidad;
                $item->save();
            } else {
                $item = CarritoItem::create([
                    'carrito_id' => $carrito->id,
                    'variante_sku_id' => $varianteSkuId,
                    'cantidad' => $cantidad,
                ]);
            }

            $this->reservaService->reservarParaCarrito(
                $existencia->id,
                $carrito->id,
                $cantidad,
                minutosVigencia: null,
            );

            return $item->fresh();
        });
    }

    /**
     * Remueve un item del carrito y libera la reserva de stock asociada.
     */
    public function removerItem(Carrito $carrito, int $varianteSkuId): bool
    {
        return DB::transaction(function () use ($carrito, $varianteSkuId): bool {
            /** @var CarritoItem|null $item */
            $item = CarritoItem::where('carrito_id', $carrito->id)
                ->where('variante_sku_id', $varianteSkuId)
                ->first();

            if ($item === null) {
                return false;
            }

            $existencia = Existencia::where('variante_sku_id', $varianteSkuId)->first();

            if ($existencia !== null) {
                $reservas = ReservaStock::where('carrito_id', $carrito->id)
                    ->where('existencia_id', $existencia->id)
                    ->where('estado', 'activa')
                    ->get();

                foreach ($reservas as $reserva) {
                    $existencia->increment('cantidad', $reserva->cantidad);
                    $reserva->update(['estado' => 'liberada']);
                }
            }

            $item->delete();

            return true;
        });
    }

    /**
     * Calcula los totales del carrito aplicando la promocion indicada si se proporciona.
     *
     * @return array{subtotal: float, descuento: float, envio: float, total: float, lineas: array<int, array{variante_sku_id: int, nombre: string, precio: float, cantidad: int, subtotal_linea: float}>}
     */
    public function calcularTotales(Carrito $carrito, ?Promocion $promocion = null): array
    {
        $items = $carrito->items()->with('varianteSku.producto')->get();

        $lineas = [];
        $subtotal = 0.0;

        foreach ($items as $item) {
            $variante = $item->varianteSku;
            $precio = (float) ($variante->precio ?? $variante->producto->precio);
            $subtotalLinea = $precio * $item->cantidad;
            $subtotal += $subtotalLinea;

            $lineas[] = [
                'variante_sku_id' => $variante->id,
                'nombre' => $variante->producto->nombre,
                'precio' => $precio,
                'cantidad' => $item->cantidad,
                'subtotal_linea' => $subtotalLinea,
            ];
        }

        $descuento = $promocion !== null
            ? $this->calcularDescuento($subtotal, $promocion)
            : 0.0;

        $envio = ($subtotal - $descuento) >= 500.0 ? 0.0 : 99.0;
        $total = $subtotal - $descuento + $envio;

        return [
            'subtotal' => round($subtotal, 2),
            'descuento' => round($descuento, 2),
            'envio' => $envio,
            'total' => round($total, 2),
            'lineas' => $lineas,
        ];
    }

    /**
     * Valida que haya stock suficiente para todos los items del carrito antes del checkout.
     *
     *
     * @throws StockInsuficienteException
     */
    public function validarStockParaCheckout(Carrito $carrito, ?int $sucursalId = null): void
    {
        DB::transaction(function () use ($carrito, $sucursalId): void {
            $items = $carrito->items()->with('varianteSku')->get();

            foreach ($items as $item) {
                $this->inventarioService->verificarStock(
                    $item->varianteSku,
                    $item->cantidad,
                    $sucursalId,
                    bloquear: true,
                );
            }
        });
    }

    /**
     * Vacia el carrito, liberando todas las reservas activas y eliminando los items.
     */
    public function vaciarCarrito(Carrito $carrito): void
    {
        DB::transaction(function () use ($carrito): void {
            $reservasActivas = ReservaStock::where('carrito_id', $carrito->id)
                ->where('estado', 'activa')
                ->with('existencia')
                ->get();

            foreach ($reservasActivas as $reserva) {
                if ($reserva->existencia !== null) {
                    $reserva->existencia->increment('cantidad', $reserva->cantidad);
                }
                $reserva->update(['estado' => 'liberada']);
            }

            CarritoItem::where('carrito_id', $carrito->id)->delete();
        });
    }

    /**
     * Calcula el monto de descuento para el subtotal dado segun el tipo de la promocion.
     */
    private function calcularDescuento(float $subtotal, Promocion $promocion): float
    {
        $descuento = match ($promocion->tipo_descuento) {
            'porcentaje' => $subtotal * ((float) $promocion->valor / 100.0),
            'monto' => min((float) $promocion->valor, $subtotal),
            default => 0.0,
        };

        return round(min($descuento, $subtotal), 2);
    }
}
