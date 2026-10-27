<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VarianteSku extends Model
{
    use HasFactory;

    protected $table = 'variantes_sku';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'producto_id',
        'sku',
        'descripcion',
        'precio',
        'activa',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'activa' => 'boolean',
        ];
    }

    /**
     * Producto padre de la variante.
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    /**
     * Existencias de esta variante distribuidas por sucursal.
     */
    public function existencias(): HasMany
    {
        return $this->hasMany(Existencia::class, 'variante_sku_id');
    }

    /**
     * Elementos en carritos de compra asociados a este SKU.
     */
    public function carritoItems(): HasMany
    {
        return $this->hasMany(CarritoItem::class, 'variante_sku_id');
    }

    /**
     * Líneas de detalle de pedidos asociados a este SKU.
     */
    public function detallePedidos(): HasMany
    {
        return $this->hasMany(DetallePedido::class, 'variante_sku_id');
    }
}
