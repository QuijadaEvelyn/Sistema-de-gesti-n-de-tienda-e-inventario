<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Número mínimo de compras completadas para obtener el nivel de Cliente Frecuente VIP
     * con acceso anticipado a Preventas Exclusivas.
     */
    public const COMPRAS_MINIMAS_FRECUENTE = 5;

    protected $table = 'clientes';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'usuario_id',
        'nombre',
        'apellidos',
        'email',
        'telefono',
    ];

    /**
     * Cuenta de usuario vinculada al cliente (si tiene acceso a la plataforma).
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Direcciones registradas para el cliente.
     */
    public function direcciones(): HasMany
    {
        return $this->hasMany(Direccion::class, 'cliente_id');
    }

    /**
     * Dirección predeterminada del cliente.
     */
    public function direccionPredeterminada(): HasOne
    {
        return $this->hasOne(Direccion::class, 'cliente_id')->where('predeterminada', true);
    }

    /**
     * Datos de membresía y fidelización como cliente frecuente.
     */
    public function clienteFrecuente(): HasOne
    {
        return $this->hasOne(ClienteFrecuente::class, 'cliente_id');
    }

    /**
     * Carrito de compras actual del cliente.
     */
    public function carrito(): HasOne
    {
        return $this->hasOne(Carrito::class, 'cliente_id');
    }

    /**
     * Pedidos realizados por el cliente.
     */
    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'cliente_id');
    }

    /**
     * Solicitudes de devolución realizadas por el cliente.
     */
    public function devoluciones(): HasMany
    {
        return $this->hasMany(Devolucion::class, 'cliente_id');
    }

    /**
     * Cuenta el total de compras válidas realizadas por el cliente (excluyendo cancelados).
     */
    public function totalComprasRealizadas(): int
    {
        if ($this->relationLoaded('pedidos')) {
            return $this->pedidos->where('estatus', '!=', 'cancelado')->count();
        }

        return (int) $this->pedidos()
            ->where('estatus', '!=', 'cancelado')
            ->count();
    }

    /**
     * Calcula cuántas compras le faltan al cliente para alcanzar el nivel de Cliente Frecuente (5 compras).
     */
    public function comprasRestantesParaFrecuente(): int
    {
        return max(0, self::COMPRAS_MINIMAS_FRECUENTE - $this->totalComprasRealizadas());
    }

    /**
     * Determina si el cliente califica como Cliente Frecuente VIP (5 o más compras realizadas
     * o membresía activa registrada en `clientes_frecuentes`).
     */
    public function esClienteFrecuente(): bool
    {
        if ($this->totalComprasRealizadas() >= self::COMPRAS_MINIMAS_FRECUENTE) {
            return true;
        }

        if ($this->relationLoaded('clienteFrecuente')) {
            return $this->clienteFrecuente !== null;
        }

        return $this->clienteFrecuente()->exists();
    }
}
