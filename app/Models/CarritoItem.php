<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarritoItem extends Model
{
    use HasFactory;

    protected $table = 'carrito_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'carrito_id',
        'variante_sku_id',
        'cantidad',
        'precio_unitario_congelado',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cantidad' => 'integer',
            'precio_unitario_congelado' => 'decimal:2',
        ];
    }

    /**
     * Resuelve el precio unitario aplicable al ítem del carrito:
     * - Si es Pieza Única (stock = 1 o registrada en piezas_unicas / edición limitada):
     *   NO aplica precio congelado; siempre devuelve el precio actual del producto/variante.
     * - Si es Producto Común y tiene `precio_unitario_congelado`, respeta el precio congelado.
     */
    public function resolverPrecioUnitario(?bool $esPiezaUnica = null): float
    {
        $variante = $this->varianteSku;
        $precioActual = (float) ($variante?->precio ?? $variante?->producto?->precio ?? 0.0);

        if ($esPiezaUnica === null && $variante !== null) {
            $esPiezaUnica = \App\Http\Controllers\CheckoutSimuladoController::esVariantePiezaUnica(
                $variante,
                (int) $this->cantidad
            );
        }

        if ($esPiezaUnica) {
            return $precioActual;
        }

        if ($this->precio_unitario_congelado !== null) {
            return (float) $this->precio_unitario_congelado;
        }

        return $precioActual;
    }

    /**
     * Carrito contenedor del artículo.
     */
    public function carrito(): BelongsTo
    {
        return $this->belongsTo(Carrito::class, 'carrito_id');
    }

    /**
     * Variante del producto asociada al artículo en el carrito.
     */
    public function varianteSku(): BelongsTo
    {
        return $this->belongsTo(VarianteSku::class, 'variante_sku_id');
    }
}
