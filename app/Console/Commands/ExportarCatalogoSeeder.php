<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Producto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportarCatalogoSeeder extends Command
{
    /**
     * El nombre y firma del comando en la consola.
     *
     * @var string
     */
    protected $signature = 'catalogo:exportar-seeder {--force : Forzar sobreescritura sin confirmación}';

    /**
     * Descripción del comando de consola.
     *
     * @var string
     */
    protected $description = 'Exporta todos los productos, variantes, existencias, metadatos y fotos de la base de datos local al seeder versionado CatalogoCuradoSeeder';

    /**
     * Ejecuta el comando de consola.
     */
    public function handle(): int
    {
        $this->info('Iniciando exportación curatorial de catálogo a seeder versionado...');

        $assetsDir = database_path('seeders/assets/productos');
        $jsonPath = database_path('seeders/assets/catalogo_curado.json');
        $seederPath = database_path('seeders/CatalogoCuradoSeeder.php');

        File::ensureDirectoryExists($assetsDir);

        $productos = Producto::query()
            ->with([
                'catalogo',
                'variantesSku.existencias.sucursal',
                'variantesSku.existencias.piezasUnicas',
                'libro',
                'disco',
                'coleccionable',
            ])
            ->orderBy('id')
            ->get();

        if ($productos->isEmpty()) {
            if (File::exists($jsonPath) && ! $this->option('force')) {
                $this->warn('No se detectaron productos en la base de datos local.');
                $this->line('Se conservó el catálogo curado existente en ['.$jsonPath.'] para prevenir pérdida de datos.');

                return self::SUCCESS;
            }

            $this->warn('La base de datos local no contiene productos para exportar.');
        }

        $imagenesCopiadas = 0;
        $datosExportados = [];

        foreach ($productos as $producto) {
            $imagenOriginal = $producto->imagen
                ?? $producto->disco?->imagen
                ?? $producto->libro?->imagen
                ?? $producto->coleccionable?->imagen;

            $imagenSincronizada = $this->sincronizarImagenAAssets($imagenOriginal, $assetsDir, $imagenesCopiadas);

            $tipoCat = $producto->catalogo?->tipo ?? 'libro';
            $nombreCat = $producto->catalogo?->nombre ?? 'Libros';

            $metadata = $this->extraerMetadatos($producto, $tipoCat);

            $variantes = $producto->variantesSku->map(function ($variante): array {
                return [
                    'sku' => $variante->sku,
                    'descripcion' => $variante->descripcion,
                    'precio' => (float) $variante->precio,
                    'activa' => (bool) $variante->activa,
                    'existencias' => $variante->existencias->map(function ($existencia): array {
                        $piezaUnica = $existencia->piezasUnicas->first();

                        return [
                            'sucursal_id' => $existencia->sucursal_id,
                            'sucursal_nombre' => $existencia->sucursal?->nombre,
                            'cantidad' => (int) $existencia->cantidad,
                            'stock_minimo' => (int) $existencia->stock_minimo,
                            'pieza_unica' => $piezaUnica ? [
                                'numero_serie' => $piezaUnica->numero_serie,
                                'certificado' => $piezaUnica->certificado,
                                'notas' => $piezaUnica->notas,
                                'estado' => $piezaUnica->estado,
                            ] : null,
                        ];
                    })->values()->all(),
                ];
            })->values()->all();

            $datosExportados[] = [
                'nombre' => $producto->nombre,
                'catalogo_tipo' => $tipoCat,
                'catalogo_nombre' => $nombreCat,
                'descripcion' => $producto->descripcion,
                'precio' => (float) $producto->precio,
                'activo' => (bool) $producto->activo,
                'imagen' => $imagenSincronizada,
                'metadata' => $metadata,
                'variantes' => $variantes,
            ];
        }

        // Guardar estructura JSON en database/seeders/assets/catalogo_curado.json
        File::put(
            $jsonPath,
            (string) json_encode($datosExportados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        // Generar o actualizar database/seeders/CatalogoCuradoSeeder.php
        $this->generarSeederClass($seederPath, count($datosExportados));

        $this->newLine();
        $this->info('✓ Catálogo exportado exitosamente con persistencia en Git:');
        $this->line('  • Total de productos curados: <comment>'.count($datosExportados).'</comment>');
        $this->line('  • Imágenes copiadas a Git assets: <comment>'.$imagenesCopiadas.'</comment> en [database/seeders/assets/productos/]');
        $this->line('  • Archivo de datos JSON: [database/seeders/assets/catalogo_curado.json]');
        $this->line('  • Seeder PHP actualizado: [database/seeders/CatalogoCuradoSeeder.php]');
        $this->newLine();
        $this->line('<info>Listo para versionar:</info> ejecuta <comment>git add database/seeders/</comment> y haz <comment>git commit / git push</comment>.');

        return self::SUCCESS;
    }

    /**
     * Localiza la imagen física en el sistema de archivos local y la copia al directorio de assets versionados.
     */
    private function sincronizarImagenAAssets(?string $imagen, string $assetsDir, int &$imagenesCopiadas): ?string
    {
        if (empty($imagen)) {
            return null;
        }

        $posiblesRutas = [
            public_path($imagen),
            public_path('/'.ltrim($imagen, '/')),
            storage_path('app/public/'.preg_replace('#^/?(public/|storage/)?#', '', $imagen)),
            storage_path('app/'.$imagen),
            base_path($imagen),
        ];

        $archivoOrigen = null;
        foreach ($posiblesRutas as $ruta) {
            if (File::exists($ruta) && ! File::isDirectory($ruta)) {
                $archivoOrigen = $ruta;
                break;
            }
        }

        if (! $archivoOrigen) {
            return $imagen;
        }

        $nombreArchivo = basename($archivoOrigen);
        $rutaDestino = $assetsDir.DIRECTORY_SEPARATOR.$nombreArchivo;

        if (! File::exists($rutaDestino) || filesize($archivoOrigen) !== filesize($rutaDestino)) {
            File::copy($archivoOrigen, $rutaDestino);
            $imagenesCopiadas++;
        }

        // Si proviene de storage/ o es un archivo local de uploads, normalizar la ruta de guardado
        if (str_contains($imagen, 'storage/productos') || str_contains($imagen, 'productos/')) {
            return 'storage/productos/'.$nombreArchivo;
        }

        return $imagen;
    }

    /**
     * Extrae los metadatos especializados del modelo correspondiente.
     *
     * @return array<string, mixed>
     */
    private function extraerMetadatos(Producto $producto, string $tipoCat): array
    {
        if ($producto->libro || $tipoCat === 'libro') {
            return [
                'tipo' => 'libro',
                'isbn' => $producto->libro?->isbn,
                'autor' => $producto->libro?->autor,
                'editorial' => $producto->libro?->editorial,
                'genero' => $producto->libro?->genero,
                'anio_publicacion' => $producto->libro?->anio_publicacion,
                'paginas' => $producto->libro?->paginas,
            ];
        }

        if ($producto->disco || $tipoCat === 'disco' || $tipoCat === 'vinilo') {
            return [
                'tipo' => 'disco',
                'artista' => $producto->disco?->artista,
                'sello' => $producto->disco?->sello,
                'genero' => $producto->disco?->genero,
                'formato' => $producto->disco?->formato ?? 'vinilo',
                'anio_lanzamiento' => $producto->disco?->anio_lanzamiento,
            ];
        }

        if ($producto->coleccionable || $tipoCat === 'coleccionable') {
            return [
                'tipo' => 'coleccionable',
                'marca' => $producto->coleccionable?->marca,
                'serie' => $producto->coleccionable?->serie,
                'categoria' => $producto->coleccionable?->categoria ?? 'historicos',
                'material' => $producto->coleccionable?->material,
                'edicion_limitada' => (bool) ($producto->coleccionable?->edicion_limitada ?? false),
            ];
        }

        return [
            'tipo' => $tipoCat,
        ];
    }

    /**
     * Genera la clase Seeder con código PHP 8.4 tipado e idempotente.
     */
    private function generarSeederClass(string $seederPath, int $totalProductos): void
    {
        $fecha = now()->toDateTimeString();

        $plantilla = <<<'PHP'
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
 * Fecha de última exportación: {{fecha}}
 * Total de productos registrados: {{totalProductos}}
 */
class CatalogoCuradoSeeder extends Seeder
{
    public const TOTAL_PRODUCTOS = {{totalProductos}};
    public const EXPORTADO_EN = '{{fecha}}';

    /**
     * Sincroniza las imágenes de assets versionados en Git hacia storage/app/public/productos
     * para asegurar que los enlaces públicos funcionen inmediatamente en cualquier máquina del equipo.
     */
    public function sincronizarImagenes(): void
    {
        $origenAssets = database_path('seeders/assets/productos');
        $destinoStorage = storage_path('app/public/productos');
        $destinoPublic = public_path('storage/productos');

        if (! File::isDirectory($origenAssets)) {
            File::ensureDirectoryExists($origenAssets);

            return;
        }

        File::ensureDirectoryExists($destinoStorage);

        foreach (File::files($origenAssets) as $archivo) {
            $nombre = $archivo->getFilename();
            $rutaDestino = $destinoStorage.DIRECTORY_SEPARATOR.$nombre;

            if (! File::exists($rutaDestino)) {
                File::copy($archivo->getPathname(), $rutaDestino);
            }

            if (File::isDirectory($destinoPublic) && ! is_link(public_path('storage'))) {
                $rutaPublic = $destinoPublic.DIRECTORY_SEPARATOR.$nombre;
                if (! File::exists($rutaPublic)) {
                    File::copy($archivo->getPathname(), $rutaPublic);
                }
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

            if (! empty($item['imagen']) && Schema::hasColumn('productos', 'imagen')) {
                $producto->imagen = $item['imagen'];
            }

            $producto->save();

            // Sincronizar metadatos según el tipo
            $meta = $item['metadata'] ?? [];
            $tipoMeta = $meta['tipo'] ?? $tipoCat;

            if ($tipoMeta === 'libro' && Schema::hasTable('libros')) {
                Libro::updateOrCreate(
                    ['producto_id' => $producto->id],
                    array_filter([
                        'isbn' => $meta['isbn'] ?? null,
                        'autor' => $meta['autor'] ?? null,
                        'editorial' => $meta['editorial'] ?? null,
                        'genero' => $meta['genero'] ?? null,
                        'anio_publicacion' => $meta['anio_publicacion'] ?? null,
                        'paginas' => $meta['paginas'] ?? null,
                    ], fn ($val) => ! is_null($val))
                );
            } elseif (($tipoMeta === 'disco' || $tipoMeta === 'vinilo') && Schema::hasTable('discos')) {
                $datosDisco = array_filter([
                    'artista' => $meta['artista'] ?? null,
                    'sello' => $meta['sello'] ?? null,
                    'genero' => $meta['genero'] ?? null,
                    'formato' => $meta['formato'] ?? 'vinilo',
                    'anio_lanzamiento' => $meta['anio_lanzamiento'] ?? null,
                ], fn ($val) => ! is_null($val));

                if (! empty($item['imagen']) && Schema::hasColumn('discos', 'imagen')) {
                    $datosDisco['imagen'] = $item['imagen'];
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

                if (! empty($item['imagen']) && Schema::hasColumn('coleccionables', 'imagen')) {
                    $datosColeccionable['imagen'] = $item['imagen'];
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
PHP;

        $codigo = str_replace(
            ['{{totalProductos}}', '{{fecha}}'],
            [(string) $totalProductos, $fecha],
            $plantilla
        );

        File::put($seederPath, $codigo);
    }
}
