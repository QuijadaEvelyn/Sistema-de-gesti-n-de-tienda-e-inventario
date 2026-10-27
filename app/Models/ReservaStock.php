<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservaStock extends Model
{
    use HasFactory;

    protected $table = 'reservas_stock';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'existencia_id',
        'pedido_id',
        'carrito_id',
        'cantidad',
        'expira_en',
        'estado',
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
            'expira_en' => 'datetime',
        ];
    }

    /**
     * Existencia de inventario reservada temporalmente.
     */
    public function existencia(): BelongsTo
    {
        return $this->belongsTo(Existencia::class, 'existencia_id');
    }

    /**
     * Pedido al que se vincula la reserva (si se originó de una orden en proceso de pago).
     */
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    /**
     * Carrito de compras al que se vincula la reserva temporal (ej. 15 minutos en checkout).
     */
    public function carrito(): BelongsTo
    {
        return $this->belongsTo(Carrito::class, 'carrito_id');
    }
}
