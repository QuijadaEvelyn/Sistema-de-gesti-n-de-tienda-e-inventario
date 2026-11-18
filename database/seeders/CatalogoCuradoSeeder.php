<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Catalogo;
use App\Models\Coleccionable;
use App\Models\Disco;
use App\Models\Existencia;
use App\Models\Libro;
use App\Models\PiezaUnica;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\VarianteSku;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

/**
 * Seeder Curatorial Versionado en Git.
 * Generado automáticamente por: php artisan catalogo:exportar-seeder
 * Fecha de última exportación: 2026-11-18 05:03:06
 * Total de productos registrados: 58
 */
class CatalogoCuradoSeeder extends Seeder
{
    public const TOTAL_PRODUCTOS = 58;
    public const EXPORTADO_EN = '2026-11-18 05:03:06';

    /**
     * Sincroniza las imágenes de assets versionados en Git hacia public/images/productos (sin symlinks)
     * y hacia storage/app/public/productos como respaldo.
     */
    public function sincronizarImagenes(): void
    {
        $origenAssets = database_path('seeders/assets/productos');
        $destinoImages = public_path('images/productos');
        $destinoStorage = storage_path('app/public/productos');

        if (! File::isDirectory($origenAssets)) {
            File::ensureDirectoryExists($origenAssets);

            return;
        }

        File::ensureDirectoryExists($destinoImages);
        File::ensureDirectoryExists($destinoStorage);

        foreach (File::files($origenAssets) as $archivo) {
            $nombre = $archivo->getFilename();

            // Destino directo primario: public/images/productos (100% compatible entre Docker y Windows)
            $rutaImages = $destinoImages.DIRECTORY_SEPARATOR.$nombre;
            if (! File::exists($rutaImages) || filesize($archivo->getPathname()) !== filesize($rutaImages)) {
                File::copy($archivo->getPathname(), $rutaImages);
            }

            // Destino secundario de respaldo: storage/app/public/productos
            $rutaStorage = $destinoStorage.DIRECTORY_SEPARATOR.$nombre;
            if (! File::exists($rutaStorage) || filesize($archivo->getPathname()) !== filesize($rutaStorage)) {
                File::copy($archivo->getPathname(), $rutaStorage);
            }
        }
    }

    /**
     * Ejecuta la población idempotente de los productos curados y sus existencias.
     */
    public function run(): void
    {
        $this->sincronizarImagenes();

        $archivoJson = database_path('seeders/assets/catalogo_curado.json');
        if (! File::exists($archivoJson)) {
            return;
        }

        $contenido = File::get($archivoJson);
        $productos = json_decode($contenido, true);

        if (! is_array($productos) || empty($productos)) {
            return;
        }

        $sucursales = Schema::hasTable('sucursales')
            ? Sucursal::all()->keyBy('nombre')
            : collect();

        $sucursalPorDefecto = $sucursales->first() ?? Sucursal::firstOrCreate(
            ['nombre' => 'Sucursal Central Bellas Artes'],
            [
                'direccion' => 'Av. Juárez 18, Centro Histórico, Cuauhtémoc, CDMX',
                'telefono' => '5555128900',
                'activa' => true,
            ]
        );

        $catalogos = Schema::hasTable('catalogos')
            ? Catalogo::all()->keyBy('tipo')
            : collect();

        foreach ($productos as $item) {
            $tipoCat = $item['catalogo_tipo'] ?? 'libro';
            $catalogo = $catalogos->get($tipoCat) ?? Catalogo::firstOrCreate(
                ['tipo' => $tipoCat],
                [
                    'nombre' => $item['catalogo_nombre'] ?? ucfirst($tipoCat),
                    'activo' => true,
                ]
            );

            // Búsqueda idempotente: primero por el SKU de alguna de sus variantes, luego por nombre
            $primerSku = $item['variantes'][0]['sku'] ?? null;
            $varianteExistente = $primerSku ? VarianteSku::where('sku', $primerSku)->first() : null;

            $producto = $varianteExistente?->producto
                ?? Producto::where('nombre', $item['nombre'])->first()
                ?? new Producto();

            $producto->fill([
                'catalogo_id' => $catalogo->id,
                'nombre' => $item['nombre'],
                'descripcion' => $item['descripcion'] ?? null,
                'precio' => $item['precio'] ?? 0.0,
                'activo' => $item['activo'] ?? true,
            ]);

            $rutaImagen = null;
            if (! empty($item['imagen'])) {
                $nombreArchivo = basename($item['imagen']);
                $rutaImagen = 'images/productos/'.$nombreArchivo;
            }

            if (! empty($rutaImagen) && Schema::hasColumn('productos', 'imagen')) {
                $producto->imagen = $rutaImagen;
            }

            $producto->save();

            // Sincronizar metadatos según el tipo
            $meta = $item['metadata'] ?? [];
            $tipoMeta = $meta['tipo'] ?? $tipoCat;

            if ($tipoMeta === 'libro' && Schema::hasTable('libros')) {
                $datosLibro = array_filter([
                    'isbn' => $meta['isbn'] ?? null,
                    'autor' => $meta['autor'] ?? null,
                    'editorial' => $meta['editorial'] ?? null,
                    'genero' => $meta['genero'] ?? null,
                    'anio_publicacion' => $meta['anio_publicacion'] ?? null,
                    'paginas' => $meta['paginas'] ?? null,
                ], fn ($val) => ! is_null($val));

                if (! empty($rutaImagen) && Schema::hasColumn('libros', 'imagen')) {
                    $datosLibro['imagen'] = $rutaImagen;
                }

                Libro::updateOrCreate(
                    ['producto_id' => $producto->id],
                    $datosLibro
                );
            } elseif (($tipoMeta === 'disco' || $tipoMeta === 'vinilo') && Schema::hasTable('discos')) {
                $datosDisco = array_filter([
                    'artista' => $meta['artista'] ?? null,
                    'sello' => $meta['sello'] ?? null,
                    'genero' => $meta['genero'] ?? null,
                    'formato' => $meta['formato'] ?? 'vinilo',
                    'anio_lanzamiento' => $meta['anio_lanzamiento'] ?? null,
                ], fn ($val) => ! is_null($val));

                if (! empty($rutaImagen) && Schema::hasColumn('discos', 'imagen')) {
                    $datosDisco['imagen'] = $rutaImagen;
                }

                Disco::updateOrCreate(
                    ['producto_id' => $producto->id],
                    $datosDisco
                );
            } elseif ($tipoMeta === 'coleccionable' && Schema::hasTable('coleccionables')) {
                $datosColeccionable = array_filter([
                    'marca' => $meta['marca'] ?? null,
                    'serie' => $meta['serie'] ?? null,
                    'categoria' => $meta['categoria'] ?? 'historicos',
                    'material' => $meta['material'] ?? null,
                    'edicion_limitada' => $meta['edicion_limitada'] ?? false,
                ], fn ($val) => ! is_null($val));

                if (! empty($rutaImagen) && Schema::hasColumn('coleccionables', 'imagen')) {
                    $datosColeccionable['imagen'] = $rutaImagen;
                }

                Coleccionable::updateOrCreate(
                    ['producto_id' => $producto->id],
                    $datosColeccionable
                );
            }

            // Registrar variantes y existencias
            $variantes = $item['variantes'] ?? [];
            if (empty($variantes)) {
                $variantes = [
                    [
                        'sku' => $primerSku ?? 'SKU-'.strtoupper(substr(md5($producto->nombre), 0, 8)),
                        'descripcion' => $producto->nombre,
                        'precio' => $producto->precio,
                        'activa' => true,
                        'existencias' => [
                            [
                                'sucursal_nombre' => $sucursalPorDefecto->nombre,
                                'cantidad' => 1,
                                'stock_minimo' => 1,
                            ],
                        ],
                    ],
                ];
            }

            foreach ($variantes as $varData) {
                $sku = $varData['sku'] ?? 'SKU-'.$producto->id;
                $variante = VarianteSku::updateOrCreate(
                    ['sku' => $sku],
                    [
                        'producto_id' => $producto->id,
                        'descripcion' => $varData['descripcion'] ?? $producto->nombre,
                        'precio' => $varData['precio'] ?? $producto->precio,
                        'activa' => $varData['activa'] ?? true,
                    ]
                );

                $existencias = $varData['existencias'] ?? [];
                if (empty($existencias)) {
                    $existencias = [
                        [
                            'sucursal_nombre' => $sucursalPorDefecto->nombre,
                            'cantidad' => 1,
                            'stock_minimo' => 1,
                        ],
                    ];
                }

                foreach ($existencias as $exData) {
                    $nombreSuc = $exData['sucursal_nombre'] ?? null;
                    $sucursal = ($nombreSuc && $sucursales->has($nombreSuc))
                        ? $sucursales->get($nombreSuc)
                        : $sucursalPorDefecto;

                    $existencia = Existencia::updateOrCreate(
                        [
                            'variante_sku_id' => $variante->id,
                            'sucursal_id' => $sucursal->id,
                        ],
                        [
                            'cantidad' => max(1, (int) ($exData['cantidad'] ?? 1)),
                            'stock_minimo' => (int) ($exData['stock_minimo'] ?? 1),
                        ]
                    );

                    if (! empty($exData['pieza_unica']) && Schema::hasTable('piezas_unicas')) {
                        $pu = $exData['pieza_unica'];
                        if (! empty($pu['numero_serie'])) {
                            PiezaUnica::updateOrCreate(
                                ['numero_serie' => $pu['numero_serie']],
                                [
                                    'existencia_id' => $existencia->id,
                                    'certificado' => $pu['certificado'] ?? null,
                                    'notas' => $pu['notas'] ?? null,
                                    'estado' => $pu['estado'] ?? 'disponible',
                                ]
                            );
                        }
                    }
                }
            }
        }
    }
}