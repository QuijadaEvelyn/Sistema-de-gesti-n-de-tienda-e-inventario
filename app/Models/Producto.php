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
     * Da de baja el producto ocultándolo del catálogo público sin borrar su historial.
     */
    public function darDeBaja(): bool
    {
        $this->activo = false;
        $guardado = $this->save();
        if ($guardado) {
            $this->variantesSku()->update(['activa' => false]);
        }

        return $guardado;
    }

    /**
     * Reactiva y da de alta el producto para que vuelva a estar visible en el catálogo público.
     */
    public function reactivar(): bool
    {
        $this->activo = true;
        $guardado = $this->save();
        if ($guardado) {
            $this->variantesSku()->update(['activa' => true]);
        }

        return $guardado;
    }

    /**
     * Filtra únicamente productos activos para la tienda y catálogo público.
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Filtra únicamente productos dados de baja / inactivos.
     */
    public function scopeDadosDeBaja($query)
    {
        return $query->where('activo', false);
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

    /**
     * Accesor para obtener la URL pública de la imagen del producto, con verificación
     * de existencia física para evitar enlaces rotos y soporte para rutas nuevas (images/)
     * y heredadas (storage/ o productos/).
     */
    public function getImagenUrlAttribute(): ?string
    {
        $ruta = $this->imagen;

        if (empty($ruta)) {
            return null;
        }

        // 1. URL externa directa (CDN o mock)
        if (str_starts_with($ruta, 'http://') || str_starts_with($ruta, 'https://')) {
            return $ruta;
        }

        // 2. Ruta directa en public/images/ (almacenamiento primario sin symlinks)
        if (str_starts_with($ruta, 'images/')) {
            if (file_exists(public_path($ruta))) {
                return asset($ruta);
            }

            $enProductos = 'images/productos/'.basename($ruta);
            if (file_exists(public_path($enProductos))) {
                return asset($enProductos);
            }
        }

        // 3. Rutas heredadas en storage/ (e.g. storage/productos/hash.ext)
        if (str_starts_with($ruta, 'storage/')) {
            $limpia = preg_replace('#^storage/#', '', $ruta);

            // Verificar si el archivo ya fue respaldado en public/images/productos/
            $enImagesProductos = 'images/productos/'.basename($ruta);
            if (file_exists(public_path($enImagesProductos))) {
                return asset($enImagesProductos);
            }

            // Verificar en public/storage
            if (file_exists(public_path('storage/'.$limpia))) {
                return asset('storage/'.ltrim($limpia, '/'));
            }

            // Verificar en storage/app/public/
            if (file_exists(storage_path('app/public/'.$limpia))) {
                return asset('storage/'.ltrim($limpia, '/'));
            }
        }

        // 4. Rutas relativas como 'productos/hash.ext'
        if (str_starts_with($ruta, 'productos/')) {
            $enImages = 'images/'.$ruta;
            if (file_exists(public_path($enImages))) {
                return asset($enImages);
            }

            $enImagesBasename = 'images/productos/'.basename($ruta);
            if (file_exists(public_path($enImagesBasename))) {
                return asset($enImagesBasename);
            }

            if (file_exists(public_path('storage/'.$ruta))) {
                return asset('storage/'.ltrim($ruta, '/'));
            }

            if (file_exists(storage_path('app/public/'.$ruta))) {
                return asset('storage/'.ltrim($ruta, '/'));
            }
        }

        // 5. Verificación genérica en public_path
        $rutaLimpia = ltrim($ruta, '/');
        if (file_exists(public_path($rutaLimpia))) {
            return asset($rutaLimpia);
        }

        // 6. Verificar si existe con el mismo nombre en public/images/productos/
        $enImagesDirecto = 'images/productos/'.basename($ruta);
        if (file_exists(public_path($enImagesDirecto))) {
            return asset($enImagesDirecto);
        }

        return null;
    }
}
