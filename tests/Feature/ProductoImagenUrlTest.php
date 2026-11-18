<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Catalogo;
use App\Models\Libro;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ProductoImagenUrlTest extends TestCase
{
    use RefreshDatabase;

    private array $archivosCreados = [];

    protected function tearDown(): void
    {
        foreach ($this->archivosCreados as $ruta) {
            if (File::exists($ruta)) {
                File::delete($ruta);
            }
        }

        parent::tearDown();
    }

    private function crearArchivoEn(string $rutaRelativa): string
    {
        $rutaCompleta = public_path($rutaRelativa);
        File::ensureDirectoryExists(dirname($rutaCompleta));
        File::put($rutaCompleta, 'DUMMY_IMAGE_BYTES');
        $this->archivosCreados[] = $rutaCompleta;

        return $rutaCompleta;
    }

    public function test_imagen_url_resuelve_ruta_nueva_en_images_productos(): void
    {
        $this->crearArchivoEn('images/productos/libro_nuevo.jpg');

        $catalogo = Catalogo::create(['nombre' => 'Libros', 'tipo' => 'libro', 'activo' => true]);
        $producto = Producto::create([
            'catalogo_id' => $catalogo->id,
            'nombre' => 'Libro Con Ruta Nueva',
            'precio' => 500.00,
            'imagen' => 'images/productos/libro_nuevo.jpg',
        ]);

        $libro = Libro::create([
            'producto_id' => $producto->id,
            'autor' => 'Autor Test',
        ]);

        $this->assertNotNull($producto->imagen_url);
        $this->assertSame(asset('images/productos/libro_nuevo.jpg'), $producto->imagen_url);
        $this->assertSame(asset('images/productos/libro_nuevo.jpg'), $libro->imagen_url);
    }

    public function test_imagen_url_resuelve_ruta_antigua_en_productos(): void
    {
        // El archivo físico está presente en public/images/productos/libro_antiguo.jpg
        $this->crearArchivoEn('images/productos/libro_antiguo.jpg');

        $catalogo = Catalogo::create(['nombre' => 'Libros', 'tipo' => 'libro', 'activo' => true]);
        $producto = Producto::create([
            'catalogo_id' => $catalogo->id,
            'nombre' => 'Libro Con Ruta Antigua productos/',
            'precio' => 450.00,
            'imagen' => 'productos/libro_antiguo.jpg',
        ]);

        $this->assertNotNull($producto->imagen_url);
        $this->assertSame(asset('images/productos/libro_antiguo.jpg'), $producto->imagen_url);
    }

    public function test_imagen_url_resuelve_ruta_antigua_storage_sin_duplicar_prefijo(): void
    {
        $this->crearArchivoEn('images/productos/portada_storage.webp');

        $catalogo = Catalogo::create(['nombre' => 'Libros', 'tipo' => 'libro', 'activo' => true]);
        $producto = Producto::create([
            'catalogo_id' => $catalogo->id,
            'nombre' => 'Libro Con Ruta Antigua storage/',
            'precio' => 600.00,
            'imagen' => 'storage/productos/portada_storage.webp',
        ]);

        $this->assertNotNull($producto->imagen_url);
        $this->assertStringNotContainsString('storage/storage', $producto->imagen_url);
        $this->assertSame(asset('images/productos/portada_storage.webp'), $producto->imagen_url);
    }

    public function test_imagen_url_resuelve_url_externa_directamente(): void
    {
        $urlExterna = 'https://images.unsplash.com/photo-1544947950-fa07a98d237f';

        $catalogo = Catalogo::create(['nombre' => 'Libros', 'tipo' => 'libro', 'activo' => true]);
        $producto = Producto::create([
            'catalogo_id' => $catalogo->id,
            'nombre' => 'Libro Con Imagen Externa',
            'precio' => 700.00,
            'imagen' => $urlExterna,
        ]);

        $this->assertSame($urlExterna, $producto->imagen_url);
    }

    public function test_imagen_url_retorna_null_si_el_archivo_fisico_no_existe(): void
    {
        $catalogo = Catalogo::create(['nombre' => 'Libros', 'tipo' => 'libro', 'activo' => true]);
        $producto = Producto::create([
            'catalogo_id' => $catalogo->id,
            'nombre' => 'Libro Con Imagen Fantasma',
            'precio' => 800.00,
            'imagen' => 'images/productos/archivo_inexistente_completamente_9999.jpg',
        ]);

        // Al no existir el archivo físico, debe devolver null para que Blade active el fallback SVG
        $this->assertNull($producto->imagen_url);
    }
}
