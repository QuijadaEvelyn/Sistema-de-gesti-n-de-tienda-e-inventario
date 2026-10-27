<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'folio',
        'cliente_id',
        'direccion_envio_id',
        'estatus',
        'subtotal',
        'descuento',
        'total',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'descuento' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    /**
     * Cliente que realizó el pedido.
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    /**
     * Dirección de entrega para el pedido (si requiere envío físico).
     */
    public function direccionEnvio(): BelongsTo
    {
        return $this->belongsTo(Direccion::class, 'direccion_envio_id');
    }

    /**
     * Líneas de detalle / artículos adquiridos en el pedido.
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(DetallePedido::class, 'pedido_id');
    }

    /**
     * Reservas de stock aseguradas para cumplir este pedido.
     */
    public function reservasStock(): HasMany
    {
        return $this->hasMany(ReservaStock::class, 'pedido_id');
    }

    /**
     * Transacciones de pago asociadas a este pedido.
     */
    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'pedido_id');
    }

    /**
     * Devoluciones tramitadas sobre artículos de este pedido.
     */
    public function devoluciones(): HasMany
    {
        return $this->hasMany(Devolucion::class, 'pedido_id');
    }

    /**
     * Guías o envíos de paquetería asociados al pedido.
     */
    public function envios(): HasMany
    {
        return $this->hasMany(Envio::class, 'pedido_id');
    }
}
