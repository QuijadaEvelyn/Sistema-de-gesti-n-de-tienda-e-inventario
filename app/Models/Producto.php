<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

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
        $candidatos = [
            $this->attributes['imagen'] ?? null,
            $this->relationLoaded('disco') ? $this->disco?->imagen : null,
            $this->relationLoaded('libro') ? $this->libro?->imagen : null,
            $this->relationLoaded('coleccionable') ? $this->coleccionable?->imagen : null,
            $this->relationLoaded('variantesSku') ? $this->variantesSku->first()?->imagen : null,
            $this->disco?->imagen ?? null,
            $this->libro?->imagen ?? null,
            $this->coleccionable?->imagen ?? null,
            self::IMAGENES_CONOCIDAS[$this->nombre] ?? null,
        ];

        foreach ($candidatos as $valor) {
            if (is_string($valor) && trim($valor) !== '') {
                return trim($valor);
            }
        }

        return null;
    }

    /**
     * Normaliza cualquier ruta de imagen (relativa a `storage/app/public`, con prefijo `storage/`
     * o en `images/`) hacia su URL pública válida, evitando duplicar `/storage/storage/`.
     */
    public static function resolverUrlImagen(?string $ruta): ?string
    {
        if ($ruta === null || trim($ruta) === '') {
            return null;
        }

        $ruta = trim($ruta);

        // 1. URL externa o absoluta ya resuelta
        if (str_starts_with($ruta, 'http://') || str_starts_with($ruta, 'https://')) {
            return preg_replace('#/storage/(storage/)+#i', '/storage/', $ruta) ?: $ruta;
        }

        $limpia = ltrim(str_replace('\\', '/', $ruta), '/');

        // 2. Rutas que ya incluyen `storage/` (evitando duplicar `/storage/storage/`)
        if (str_starts_with(strtolower($limpia), 'storage/')) {
            $relativaStorage = preg_replace('#^(storage/)+#i', '', $limpia) ?: '';
            $relativaStorage = ltrim($relativaStorage, '/');

            if ($relativaStorage === '') {
                return null;
            }

            if (file_exists(storage_path('app/public/'.$relativaStorage))
                || file_exists(public_path('storage/'.$relativaStorage))
                || Storage::disk('public')->exists($relativaStorage)) {
                return asset('storage/'.$relativaStorage);
            }

            $enImagesProductos = 'images/productos/'.basename($relativaStorage);
            if (file_exists(public_path($enImagesProductos))) {
                return asset($enImagesProductos);
            }

            return null;
        }

        // 3. Rutas en `public/images/` (respaldo directo o catálogo base)
        if (str_starts_with(strtolower($limpia), 'images/')) {
            if (file_exists(public_path($limpia))) {
                return asset($limpia);
            }

            $nombreBase = basename($limpia);
            if (file_exists(storage_path('app/public/productos/'.$nombreBase))
                || file_exists(public_path('storage/productos/'.$nombreBase))
                || Storage::disk('public')->exists('productos/'.$nombreBase)) {
                return asset('storage/productos/'.$nombreBase);
            }

            $enProductos = 'images/productos/'.$nombreBase;
            if (file_exists(public_path($enProductos))) {
                return asset($enProductos);
            }

            return null;
        }

        // 4. Rutas relativas a `storage/app/public` (ej. `productos/archivo.jpg`)
        if (file_exists(storage_path('app/public/'.$limpia))
            || file_exists(public_path('storage/'.$limpia))
            || Storage::disk('public')->exists($limpia)) {
            return asset('storage/'.$limpia);
        }

        if (file_exists(public_path($limpia))) {
            return asset($limpia);
        }

        $nombreBase = basename($limpia);
        if (file_exists(storage_path('app/public/productos/'.$nombreBase))
            || file_exists(public_path('storage/productos/'.$nombreBase))
            || Storage::disk('public')->exists('productos/'.$nombreBase)) {
            return asset('storage/productos/'.$nombreBase);
        }

        $enImagesDirecto = 'images/productos/'.$nombreBase;
        if (file_exists(public_path($enImagesDirecto))) {
            return asset($enImagesDirecto);
        }

        return null;
    }

    /**
     * Accesor para obtener la URL pública de la imagen del producto, manejando tanto
     * rutas relativas a `storage/app/public` como rutas con prefijo `storage/` o `images/`
     * sin duplicar `/storage/storage/`.
     */
    public function getImagenUrlAttribute(): ?string
    {
        return self::resolverUrlImagen($this->imagen);
    }
}
