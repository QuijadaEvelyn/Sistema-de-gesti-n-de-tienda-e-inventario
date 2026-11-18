<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Catalogo;
use App\Models\Existencia;
use App\Models\Libro;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\VarianteSku;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SincronizacionCatalogoSeederTest extends TestCase
{
    use RefreshDatabase;

    private string $testImageName = 'test_seeder_portada.jpg';

    private ?string $backupJsonContent = null;

    protected function setUp(): void
    {
        parent::setUp();

        // Respaldar catalogo_curado.json original si existía antes del test
        $jsonPath = database_path('seeders/assets/catalogo_curado.json');
        if (File::exists($jsonPath)) {
            $this->backupJsonContent = File::get($jsonPath);
        }
    }

    protected function tearDown(): void
    {
        // Limpiar archivos temporales de prueba
        $testAsset = database_path('seeders/assets/productos/'.$this->testImageName);
        if (File::exists($testAsset)) {
            File::delete($testAsset);
        }

        $testStorage = storage_path('app/public/productos/'.$this->testImageName);
        if (File::exists($testStorage)) {
            File::delete($testStorage);
        }

        $testPublicImages = public_path('images/productos/'.$this->testImageName);
        if (File::exists($testPublicImages)) {
            File::delete($testPublicImages);
        }

        // Restaurar JSON original si existía
        $jsonPath = database_path('seeders/assets/catalogo_curado.json');
        if ($this->backupJsonContent !== null) {
            File::put($jsonPath, $this->backupJsonContent);
        }

        parent::tearDown();
    }

    public function test_comando_catalogo_exportar_seeder_genera_json_y_seeder_con_datos_e_imagenes(): void
    {
        Storage::fake('public');

        // 1. Crear imagen física simulada en storage/app/public/productos
        $rutaStorageDir = storage_path('app/public/productos');
        File::ensureDirectoryExists($rutaStorageDir);
        $rutaImagenStorage = $rutaStorageDir.DIRECTORY_SEPARATOR.$this->testImageName;
        File::put($rutaImagenStorage, 'DATOS_FALSOS_IMAGEN_DE_PRUEBA_12345');

        // 2. Crear datos de catálogo en base de datos
        $sucursal = Sucursal::firstOrCreate(
            ['nombre' => 'Sucursal Central Bellas Artes'],
            [
                'direccion' => 'Av. Juárez 18, CDMX',
                'telefono' => '5555128900',
                'activa' => true,
            ]
        );

        $catLibro = Catalogo::firstOrCreate(
            ['tipo' => 'libro'],
            ['nombre' => 'Libros', 'activo' => true]
        );

        $producto = Producto::create([
            'catalogo_id' => $catLibro->id,
            'nombre' => 'Libro Curado de Prueba Especial',
            'descripcion' => 'Edición especial de curaduría para pruebas de sincronización.',
            'precio' => 1500.00,
            'activo' => true,
            'imagen' => 'storage/productos/'.$this->testImageName,
        ]);

        Libro::create([
            'producto_id' => $producto->id,
            'autor' => 'Curador de Archivo',
            'editorial' => 'Archivum Press',
            'genero' => 'Historia',
            'anio_publicacion' => 2026,
            'paginas' => 320,
            'isbn' => '978-607-00-9999-1',
        ]);

        $variante = VarianteSku::create([
            'producto_id' => $producto->id,
            'sku' => 'LIB-TEST-SYNC-001',
            'descripcion' => 'Primera Tirada Curada',
            'precio' => 1500.00,
            'activa' => true,
        ]);

        Existencia::create([
            'variante_sku_id' => $variante->id,
            'sucursal_id' => $sucursal->id,
            'cantidad' => 7,
            'stock_minimo' => 1,
        ]);

        // 3. Ejecutar comando de exportación
        $this->artisan('catalogo:exportar-seeder', ['--force' => true])
            ->assertExitCode(0)
            ->expectsOutputToContain('Catálogo exportado exitosamente con persistencia en Git');

        // 4. Verificar que la imagen se copió a database/seeders/assets/productos/
        $imagenDestinoGit = database_path('seeders/assets/productos/'.$this->testImageName);
        $this->assertFileExists($imagenDestinoGit);
        $this->assertSame('DATOS_FALSOS_IMAGEN_DE_PRUEBA_12345', File::get($imagenDestinoGit));

        // 5. Verificar que catalogo_curado.json contiene el producto y variantes
        $jsonPath = database_path('seeders/assets/catalogo_curado.json');
        $this->assertFileExists($jsonPath);
        $jsonContenido = File::get($jsonPath);
        $this->assertStringContainsString('LIB-TEST-SYNC-001', $jsonContenido);
        $this->assertStringContainsString('Libro Curado de Prueba Especial', $jsonContenido);
        $this->assertStringContainsString('Curador de Archivo', $jsonContenido);
        $this->assertStringContainsString('Archivum Press', $jsonContenido);

        // 6. Verificar que CatalogoCuradoSeeder.php fue generado
        $seederPath = database_path('seeders/CatalogoCuradoSeeder.php');
        $this->assertFileExists($seederPath);
        $seederContenido = File::get($seederPath);
        $this->assertStringContainsString('class CatalogoCuradoSeeder extends Seeder', $seederContenido);
        $this->assertStringContainsString('sincronizarImagenes', $seederContenido);
    }

    public function test_catalogo_curado_seeder_es_idempotente_y_restaura_productos_con_imagenes(): void
    {
        // 1. Preparar un JSON de prueba
        $jsonPath = database_path('seeders/assets/catalogo_curado.json');
        $datosPrueba = [
            [
                'nombre' => 'Vinilo Curado de Alta Fidelidad',
                'catalogo_tipo' => 'disco',
                'catalogo_nombre' => 'Discos',
                'descripcion' => 'Prensado audiófilo masterizado.',
                'precio' => 2400.00,
                'activo' => true,
                'imagen' => 'storage/productos/'.$this->testImageName,
                'metadata' => [
                    'tipo' => 'disco',
                    'artista' => 'Ensamble de Jazz Archivum',
                    'sello' => 'Archivum Records',
                    'genero' => 'Jazz Modal',
                    'formato' => 'vinilo',
                    'anio_lanzamiento' => 2026,
                ],
                'variantes' => [
                    [
                        'sku' => 'VIN-TEST-SYNC-999',
                        'descripcion' => 'Edición Limitada 180g',
                        'precio' => 2400.00,
                        'activa' => true,
                        'existencias' => [
                            [
                                'sucursal_nombre' => 'Sucursal Central Bellas Artes',
                                'cantidad' => 4,
                                'stock_minimo' => 1,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        File::put($jsonPath, (string) json_encode($datosPrueba, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Colocar imagen en database/seeders/assets/productos/
        $assetsDir = database_path('seeders/assets/productos');
        File::ensureDirectoryExists($assetsDir);
        File::put($assetsDir.DIRECTORY_SEPARATOR.$this->testImageName, 'CONTENIDO_VINILO_AUDIOPHILE');

        // Asegurar que no exista previamente en images ni en storage
        $destinoImages = public_path('images/productos/'.$this->testImageName);
        if (File::exists($destinoImages)) {
            File::delete($destinoImages);
        }

        $destinoStorage = storage_path('app/public/productos/'.$this->testImageName);
        if (File::exists($destinoStorage)) {
            File::delete($destinoStorage);
        }

        // 2. Ejecutar seeder por primera vez
        $this->artisan('db:seed', ['--class' => 'CatalogoCuradoSeeder'])
            ->assertExitCode(0);

        // Verificar que la imagen fue copiada automáticamente a public/images/productos (sin symlinks)
        $this->assertFileExists($destinoImages);
        $this->assertSame('CONTENIDO_VINILO_AUDIOPHILE', File::get($destinoImages));

        // Verificar existencia en BD con ruta limpia images/productos/
        $this->assertDatabaseHas('productos', [
            'nombre' => 'Vinilo Curado de Alta Fidelidad',
            'imagen' => 'images/productos/'.$this->testImageName,
            'precio' => 2400.00,
        ]);

        $this->assertDatabaseHas('discos', [
            'artista' => 'Ensamble de Jazz Archivum',
            'sello' => 'Archivum Records',
        ]);

        $this->assertDatabaseHas('variantes_sku', [
            'sku' => 'VIN-TEST-SYNC-999',
        ]);

        $this->assertDatabaseHas('existencias', [
            'cantidad' => 4,
        ]);

        // 3. Ejecutar el seeder por SEGUNDA VEZ para garantizar idempotencia estricta
        $this->artisan('db:seed', ['--class' => 'CatalogoCuradoSeeder'])
            ->assertExitCode(0);

        // Los conteos deben mantenerse iguales (sin registros duplicados)
        $this->assertSame(1, Producto::where('nombre', 'Vinilo Curado de Alta Fidelidad')->count());
        $this->assertSame(1, VarianteSku::where('sku', 'VIN-TEST-SYNC-999')->count());
    }

    public function test_database_seeder_completo_ejecuta_sin_errores(): void
    {
        $this->artisan('db:seed')
            ->assertExitCode(0);

        $this->assertDatabaseHas('sucursales', [
            'nombre' => 'Sucursal Central Bellas Artes',
        ]);

        $this->assertDatabaseHas('catalogos', [
            'tipo' => 'libro',
        ]);

        $this->assertDatabaseHas('catalogos', [
            'tipo' => 'disco',
        ]);

        $this->assertDatabaseHas('catalogos', [
            'tipo' => 'coleccionable',
        ]);
    }
}
