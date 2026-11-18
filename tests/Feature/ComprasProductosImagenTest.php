<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Catalogo;
use App\Models\Producto;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\VarianteSku;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ComprasProductosImagenTest extends TestCase
{
    use RefreshDatabase;

    private function crearUsuarioConRol(string $nombreRol): Usuario
    {
        $rol = Rol::firstOrCreate(
            ['nombre' => $nombreRol],
            ['descripcion' => "Rol {$nombreRol}"]
        );

        $usuario = Usuario::factory()->create([
            'activo' => true,
        ]);

        $usuario->roles()->syncWithoutDetaching([$rol->id]);

        return $usuario;
    }

    private function crearArchivoImagen(string $nombre = 'portada.jpg', int $extraBytes = 0): UploadedFile
    {
        // 1x1 JPEG válido sin requerir la extensión PHP GD
        $jpegBase64 = '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=';
        $content = base64_decode($jpegBase64).str_repeat('A', $extraBytes);

        return UploadedFile::fake()->createWithContent($nombre, $content);
    }

    private function crearArchivoPng(string $nombre = 'portada.png'): UploadedFile
    {
        // 1x1 PNG válido sin requerir la extensión PHP GD
        $pngBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        return UploadedFile::fake()->createWithContent($nombre, base64_decode($pngBase64));
    }

    private function crearCatalogo(string $nombre = 'Discos'): Catalogo
    {
        return Catalogo::firstOrCreate(
            ['nombre' => $nombre],
            ['tipo' => 'disco', 'activo' => true]
        );
    }

    public function test_vista_compras_productos_muestra_zona_de_subida_de_imagen_y_modal(): void
    {
        $usuario = $this->crearUsuarioConRol('compras');

        $response = $this->actingAs($usuario)->get(route('compras.productos'));

        $response->assertOk();
        $response->assertSee('Alta Individual de Producto');
        $response->assertSee('Imagen del Producto (JPG, PNG, WEBP · Máx 4MB)');
        $response->assertSee('input_imagen_producto', false);
        $response->assertSee('dropzone_imagen_producto', false);
        $response->assertSee('modal_actualizar_imagen', false);
        $response->assertSee('enctype="multipart/form-data"', false);
    }

    public function test_compras_puede_dar_de_alta_producto_con_imagen_y_se_guarda_en_disco_public_y_bitacora(): void
    {
        Storage::fake('public');
        $usuario = $this->crearUsuarioConRol('compras');
        $catalogo = $this->crearCatalogo('Libros');

        $archivoImagen = $this->crearArchivoImagen('rayuela_portada.jpg');

        $payload = [
            'catalogo_id' => $catalogo->id,
            'sku' => 'LIB-RAY-001',
            'nombre' => 'Rayuela - Primera Edición Sudamericana 1963',
            'descripcion' => 'Ejemplar con dedicatoria manuscrita de Julio Cortázar.',
            'precio' => 45000.00,
            'imagen' => $archivoImagen,
        ];

        $response = $this->actingAs($usuario)->post(route('compras.productos.store'), $payload);

        $response->assertRedirect(route('compras.productos'));
        $response->assertSessionHas('status');

        $producto = Producto::where('nombre', 'Rayuela - Primera Edición Sudamericana 1963')->first();
        $this->assertNotNull($producto);
        $this->assertNotNull($producto->imagen);
        $this->assertStringStartsWith('storage/productos/', $producto->imagen);

        // Verificar archivo en disco público
        $rutaAlmacenada = str_replace('storage/', '', $producto->imagen);
        Storage::disk('public')->assertExists($rutaAlmacenada);

        // Verificar variante SKU asociada
        $variante = VarianteSku::where('producto_id', $producto->id)->first();
        $this->assertNotNull($variante);
        $this->assertEquals($producto->imagen, $variante->imagen);

        // Verificar existencia inicial creada con al menos 1 unidad
        $this->assertDatabaseHas('existencias', [
            'variante_sku_id' => $variante->id,
        ]);
        $this->assertTrue($variante->existencias()->sum('cantidad') >= 1);

        // Verificar que aparece de inmediato en el Sitio Público
        $publicResponse = $this->get(route('public.home'));
        $publicResponse->assertOk();
        $publicResponse->assertSee('Rayuela - Primera Edición Sudamericana 1963');

        // Verificar registro de auditoría en Bitácora
        $this->assertDatabaseHas('bitacoras', [
            'usuario_id' => $usuario->id,
            'accion' => 'PRODUCTO_CREADO_CON_IMAGEN',
            'tabla_afectada' => 'productos',
            'registro_id' => $producto->id,
        ]);
    }

    public function test_compras_puede_dar_de_alta_producto_sin_imagen(): void
    {
        $usuario = $this->crearUsuarioConRol('compras');
        $catalogo = $this->crearCatalogo('Coleccionables');

        $payload = [
            'catalogo_id' => $catalogo->id,
            'sku' => 'COL-EVA-001',
            'nombre' => 'Figura Evangelion EVA-01',
            'descripcion' => 'Figura metálica con certificado de autenticidad.',
            'precio' => 12500.00,
        ];

        $response = $this->actingAs($usuario)->post(route('compras.productos.store'), $payload);

        $response->assertRedirect(route('compras.productos'));
        $producto = Producto::where('sku', 'COL-EVA-001')->orWhere('nombre', 'Figura Evangelion EVA-01')->first();
        $this->assertNotNull($producto);
        $this->assertNull($producto->attributes['imagen'] ?? null);

        // Verificar bitácora de producto creado sin imagen
        $this->assertDatabaseHas('bitacoras', [
            'usuario_id' => $usuario->id,
            'accion' => 'PRODUCTO_CREADO',
            'tabla_afectada' => 'productos',
            'registro_id' => $producto->id,
        ]);
    }

    public function test_compras_puede_actualizar_imagen_de_producto_existente(): void
    {
        Storage::fake('public');
        $usuario = $this->crearUsuarioConRol('compras');
        $catalogo = $this->crearCatalogo('Discos');

        // Crear producto previo con imagen antigua
        $imagenAntigua = $this->crearArchivoImagen('antigua.jpg');
        $rutaAntigua = Storage::disk('public')->putFile('productos', $imagenAntigua);

        $producto = Producto::create([
            'catalogo_id' => $catalogo->id,
            'nombre' => 'Kind of Blue - Miles Davis',
            'precio' => 18500.00,
            'activo' => true,
            'imagen' => 'storage/'.$rutaAntigua,
        ]);

        $variante = VarianteSku::create([
            'producto_id' => $producto->id,
            'sku' => 'DIS-KOB-001',
            'descripcion' => 'Kind of Blue - Miles Davis',
            'precio' => 18500.00,
            'activa' => true,
            'imagen' => 'storage/'.$rutaAntigua,
        ]);

        Storage::disk('public')->assertExists($rutaAntigua);

        // Subir nueva imagen
        $nuevaImagen = $this->crearArchivoPng('nueva_portada.png');

        $response = $this->actingAs($usuario)->post(
            route('compras.productos.actualizar-imagen', $producto),
            ['imagen' => $nuevaImagen]
        );

        $response->assertRedirect(route('compras.productos'));
        $response->assertSessionHas('status');

        $producto->refresh();
        $variante->refresh();

        $this->assertNotEquals('storage/'.$rutaAntigua, $producto->imagen);
        $this->assertStringStartsWith('storage/productos/', $producto->imagen);
        $this->assertEquals($producto->imagen, $variante->imagen);

        // La imagen antigua debe haber sido removida del disco
        Storage::disk('public')->assertMissing($rutaAntigua);

        // La nueva imagen debe existir
        $nuevaRutaAlmacenada = str_replace('storage/', '', $producto->imagen);
        Storage::disk('public')->assertExists($nuevaRutaAlmacenada);

        // Bitácora registrada
        $this->assertDatabaseHas('bitacoras', [
            'usuario_id' => $usuario->id,
            'accion' => 'PRODUCTO_IMAGEN_ACTUALIZADA',
            'tabla_afectada' => 'productos',
            'registro_id' => $producto->id,
        ]);
    }

    public function test_validacion_rechaza_archivos_no_imagen_o_que_excedan_4mb(): void
    {
        Storage::fake('public');
        $usuario = $this->crearUsuarioConRol('compras');
        $catalogo = $this->crearCatalogo('Libros');

        // 1. Archivo que no es imagen
        $archivoPdf = UploadedFile::fake()->create('documento.pdf', 500, 'application/pdf');

        $response = $this->actingAs($usuario)->post(route('compras.productos.store'), [
            'catalogo_id' => $catalogo->id,
            'sku' => 'LIB-TEST-PDF',
            'nombre' => 'Libro Inválido PDF',
            'precio' => 1000.00,
            'imagen' => $archivoPdf,
        ]);

        $response->assertSessionHasErrors(['imagen']);

        // 2. Imagen mayor a 4096KB (e.g. 4.5 MB)
        $archivoGigante = $this->crearArchivoImagen('pesada.jpg', 4600 * 1024);

        $response = $this->actingAs($usuario)->post(route('compras.productos.store'), [
            'catalogo_id' => $catalogo->id,
            'sku' => 'LIB-TEST-HEAVY',
            'nombre' => 'Libro Imagen Pesada',
            'precio' => 1000.00,
            'imagen' => $archivoGigante,
        ]);

        $response->assertSessionHasErrors(['imagen']);
    }
}
