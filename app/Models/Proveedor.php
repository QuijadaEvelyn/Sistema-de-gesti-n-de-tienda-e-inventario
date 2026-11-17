<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'proveedores';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'razon_social',
        'rfc',
        'contacto_nombre',
        'email',
        'telefono',
        'direccion',
        'activo',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Relación con los movimientos de inventario recepcionados.
     */
    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'proveedor_id');
    }

    /**
     * Scope para filtrar únicamente proveedores activos.
     *
     * @param  Builder<Proveedor>  $query
     * @return Builder<Proveedor>
     */
    public function scopeActivo(Builder $query): Builder
    {
        return $query->where('activo', true);
    }
}
