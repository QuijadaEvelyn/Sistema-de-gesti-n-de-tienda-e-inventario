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
     * Catálogo referencial de imágenes predeterminadas para vinilos y coleccionables.
     */
    public const IMAGENES_CONOCIDAS = [
        'Las Cabras Montañesas - Góticos (2 LP)' => 'images/coleccionables/las-cabras-montañeras.jpg',
        'Los Yaki – Los Grandes Años Del Rock' => 'images/coleccionables/Los-Yaki.jpg',
        'Caifanes – Caifanes (LP, Edición Original Venezuela)' => 'images/coleccionables/Caifanes.jpg',
        'Mägo de Oz - La Ciudad de los Árboles (Lp Vinyl)' => 'images/coleccionables/Mago-de-oz-La-ciudad.jpg',
        'Mägo de Oz - Alicia En El Pais Del Metalverso (Lp Vinyl)' => 'images/coleccionables/Mago-de-oz-Alicia.jpg',
        'Los Grandes Románticos de la Balada (3CDs)' => 'images/coleccionables/Los-Grandes-Romanticos.jpg',
        'La Arrolladora Banda El Limón - Las Bandas Románticas' => 'images/coleccionables/Cd-la-arrolladora.jpg',
        'Juan Gabriel & Rocío Dúrcal - Juntos Otra Vez (2CD)' => 'images/coleccionables/Juan-Gabriel.jpg',
        'La Sonora Santanera - Tesoros de Colección Vol. 3 (3 Discos)' => 'images/coleccionables/3-cds-La-Sonora.jpg',
        'Los Mejores Huapangos | CD Música' => 'images/coleccionables/Huapangos.jpg',
        'Taylor Swift - Midnights (Korea Edition)' => 'images/coleccionables/TAYLO.jpg',
    ];

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
        'es_preventa',
        'limite_preventa',
        'preventa_exclusiva_hasta',
        'imagen',
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
            'es_preventa' => 'boolean',
            'limite_preventa' => 'integer',
            'preventa_exclusiva_hasta' => 'datetime',
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

    /**
     * Promociones y lanzamientos de preventa vinculados a este producto.
     */
    public function promociones(): HasMany
    {
        return $this->hasMany(Promocion::class, 'producto_id');
    }

    /**
     * Accesor para obtener la ruta de la imagen del producto (directa, de su relación o fallback curatorial).
     */
    public function getImagenAttribute(): ?string
    {
        return $this->attributes['imagen']
            ?? $this->disco?->imagen
            ?? $this->libro?->imagen
            ?? $this->coleccionable?->imagen
            ?? self::IMAGENES_CONOCIDAS[$this->nombre]
            ?? null;
    }
}
