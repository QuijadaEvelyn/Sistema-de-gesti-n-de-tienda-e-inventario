<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carrito extends Model
{
    use HasFactory;

    protected $table = 'carritos';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'cliente_id',
    ];

    /**
     * Cliente propietario del carrito.
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    /**
     * Artículos contenidos dentro del carrito.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CarritoItem::class, 'carrito_id');
    }

    /**
     * Reservas temporales de stock vinculadas a este carrito de compras.
     */
    public function reservasStock(): HasMany
    {
        return $this->hasMany(ReservaStock::class, 'carrito_id');
    }
}
