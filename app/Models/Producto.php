<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'productos';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'catalogo_id',
        'nombre',
        'descripcion',
        'precio',
        'activo',
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
            'activo' => 'boolean',
        ];
    }

    /**
     * Catálogo al que pertenece el producto.
     */
    public function catalogo(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'catalogo_id');
    }

    /**
     * Detalle especializado si el producto es un libro.
     */
    public function libro(): HasOne
    {
        return $this->hasOne(Libro::class, 'producto_id');
    }

    /**
     * Detalle especializado si el producto es un disco / vinilo.
     */
    public function disco(): HasOne
    {
        return $this->hasOne(Disco::class, 'producto_id');
    }

    /**
     * Detalle especializado si el producto es un coleccionable / memorabilia.
     */
    public function coleccionable(): HasOne
    {
        return $this->hasOne(Coleccionable::class, 'producto_id');
    }

    /**
     * Variantes y SKUs generados para el producto.
     */
    public function variantesSku(): HasMany
    {
        return $this->hasMany(VarianteSku::class, 'producto_id');
    }
}
