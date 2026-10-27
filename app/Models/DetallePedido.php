<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DetallePedido extends Model
{
    use HasFactory;

    protected $table = 'detalle_pedidos';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'pedido_id',
        'variante_sku_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
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
            'precio_unitario' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    /**
     * Pedido al que pertenece esta línea de detalle.
     */
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    /**
     * Variante del producto adquirida.
     */
    public function varianteSku(): BelongsTo
    {
        return $this->belongsTo(VarianteSku::class, 'variante_sku_id');
    }

    /**
     * Devoluciones solicitadas para este detalle de pedido.
     */
    public function devoluciones(): HasMany
    {
        return $this->hasMany(Devolucion::class, 'detalle_pedido_id');
    }

    /**
     * Registros de despacho en envíos correspondientes a este artículo.
     */
    public function detalleEnvios(): HasMany
    {
        return $this->hasMany(DetalleEnvio::class, 'detalle_pedido_id');
    }
}
