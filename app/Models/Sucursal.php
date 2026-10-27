<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sucursal extends Model
{
    use HasFactory;

    protected $table = 'sucursales';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
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
            'activa' => 'boolean',
        ];
    }

    /**
     * Inventario de existencias resguardado en esta sucursal.
     */
    public function existencias(): HasMany
    {
        return $this->hasMany(Existencia::class, 'sucursal_id');
    }

    /**
     * Envíos despachados desde esta sucursal de origen.
     */
    public function envios(): HasMany
    {
        return $this->hasMany(Envio::class, 'sucursal_id');
    }
}
