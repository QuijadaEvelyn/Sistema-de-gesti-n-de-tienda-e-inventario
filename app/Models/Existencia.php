<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Existencia extends Model
{
    use HasFactory;

    protected $table = 'existencias';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'variante_sku_id',
        'sucursal_id',
        'cantidad',
        'stock_minimo',
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
            'stock_minimo' => 'integer',
        ];
    }

    /**
     * Variante del producto asociada al stock.
     */
    public function varianteSku(): BelongsTo
    {
        return $this->belongsTo(VarianteSku::class, 'variante_sku_id');
    }

    /**
     * Sucursal física o bodega donde se ubica el stock.
     */
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    /**
     * Ejemplares únicos individualmente identificados bajo este registro de existencia.
     */
    public function piezasUnicas(): HasMany
    {
        return $this->hasMany(PiezaUnica::class, 'existencia_id');
    }

    /**
     * Movimientos históricos de inventario (entradas, salidas, traspasos, etc.).
     */
    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'existencia_id');
    }

    /**
     * Reservas temporales activas o históricas vinculadas a este stock.
     */
    public function reservasStock(): HasMany
    {
        return $this->hasMany(ReservaStock::class, 'existencia_id');
    }
}
