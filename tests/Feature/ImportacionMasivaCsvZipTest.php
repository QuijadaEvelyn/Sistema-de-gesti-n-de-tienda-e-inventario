<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Catalogo;
use App\Models\Existencia;
use App\Models\PiezaUnica;
use App\Models\Producto;
use App\Models\Rol;
use App\Models\Sucursal;
use App\Models\Usuario;
use App\Models\VarianteSku;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;
use ZipArchive;

class ImportacionMasivaCsvZipTest extends TestCase
{
    use RefreshDatabase;

    private function crearAdmin(): Usuario
    {
        $rol = Rol::firstOrCreate(['nombre' => 'administrador'], ['descripcion' => 'Administrador']);
        $usuario = Usuario::factory()->create(['activo' => true]);
        $usuario->roles()->syncWithoutDetaching([$rol->id]);

        return $usuario;
    }

    public function test_admin_puede_descargar_plantilla_oficial_csv(): void
    {
        $admin = $this->crearAdmin();

        $response = $this->actingAs($admin)->get(route('admin.productos.plantilla-csv'));

        $response->assertOk();
        $content = $response->streamedContent();
        $this->assertStringContainsString(
            'sku,titulo,autor_artista,categoria,sede,precio,existencias,es_pieza_unica,codigo_coa,descripcion,archivo_imagen',
            $content
        );
        $this->assertStringContainsString('LIB-EJEMPLO-001', $content);
    }

    public function test_importacion_masiva_csv_con_zip_crea_productos_fuerza_pieza_unica_a_1_y_reporta_errores_por_fila(): void
    {
        Storage::fake('public');
        $admin = $this->crearAdmin();

        $catLibros = Catalogo::create(['nombre' => 'Libros', 'tipo' => 'libro', 'activo' => true]);
        $catDiscos = Catalogo::create(['nombre' => 'Discos', 'tipo' => 'disco', 'activo' => true]);
        $sucursal = Sucursal::create([
            'nombre' => 'Sucursal Central Bellas Artes',
            'direccion' => 'Av. Juárez 18',
            'telefono' => '5555128900',
            'activa' => true,
        ]);

        // Crear ZIP temporal con 2 imágenes válidas (JPG y PNG)
        $rutaZip = sys_get_temp_dir().DIRECTORY_SEPARATOR.'test_imgs_'.uniqid('', true).'.zip';
        $zip = new ZipArchive;
        $this->assertTrue($zip->open($rutaZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true);

        $jpegBytes = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
        $pngBytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $zip->addFromString('libro_unico.jpg', $jpegBytes);
        $zip->addFromString('disco_comun.png', $pngBytes);
        $zip->close();

        // CSV:
        // Fila 2: Pieza única con existencias=5 (debe forzarse a 1) e imagen del ZIP
        // Fila 3: Producto común con existencias=8 e imagen del ZIP
        // Fila 4: Error (SKU vacío)
        // Fila 5: Error (Categoría inválida)
        $csvContenido = implode("\n", [
            'sku,titulo,autor_artista,categoria,sede,precio,existencias,es_pieza_unica,codigo_coa,descripcion,archivo_imagen',
            'CSV-LIB-001,"El Aleph Firmado","Jorge Luis Borges",Libros,"Sucursal Central Bellas Artes",4500.00,5,1,COA-999,"Primera edición.",libro_unico.jpg',
            'CSV-DIS-002,"Abbey Road 180g","The Beatles",Discos,"Sucursal Central Bellas Artes",950.50,8,0,,"Vinilo nuevo.",disco_comun.png',
            ',Sin SKU,Autor,Libros,"Sucursal Central Bellas Artes",100.00,2,0,,,',
            'CSV-ERR-004,"Producto Inválido",Autor,CategoriaInexistente,"Sucursal Central Bellas Artes",200.00,2,0,,,',
        ]);

        $fileCsv = UploadedFile::fake()->createWithContent('productos.csv', $csvContenido);
        $fileZip = new UploadedFile($rutaZip, 'imagenes.zip', 'application/zip', null, true);

        $response = $this->actingAs($admin)->post(route('admin.productos.importar'), [
            'archivo_csv' => $fileCsv,
            'archivo_zip' => $fileZip,
        ]);

        @unlink($rutaZip);

        $response->assertRedirect(route('admin.productos.index'));
        $response->assertSessionHas('importacion_resumen', function (array $resumen): bool {
            return $resumen['creados'] === 2
                && $resumen['actualizados'] === 0
                && $resumen['omitidos'] === 2
                && $resumen['imagenes_extraidas'] === 2
                && count($resumen['errores']) === 2
                && str_contains($resumen['errores'][0], 'Fila 4')
                && str_contains($resumen['errores'][1], 'Fila 5');
        });

        // Verificar Pieza Única forzada a 1 existencia + registro en piezas_unicas + imagen extraída
        $varUnica = VarianteSku::where('sku', 'CSV-LIB-001')->first();
        $this->assertNotNull($varUnica);
        $prodUnico = $varUnica->producto;
        $this->assertSame($catLibros->id, $prodUnico->catalogo_id);
        $this->assertStringStartsWith('productos/', (string) $prodUnico->imagen);
        Storage::disk('public')->assertExists((string) $prodUnico->imagen);

        $existenciaUnica = Existencia::where('variante_sku_id', $varUnica->id)
            ->where('sucursal_id', $sucursal->id)
            ->first();
        $this->assertNotNull($existenciaUnica);
        $this->assertSame(1, (int) $existenciaUnica->cantidad);
        $this->assertTrue(PiezaUnica::where('existencia_id', $existenciaUnica->id)->where('certificado', 'COA-999')->exists());

        // Verificar Producto Común con 8 existencias + imagen extraída
        $varComun = VarianteSku::where('sku', 'CSV-DIS-002')->first();
        $this->assertNotNull($varComun);
        $prodComun = $varComun->producto;
        $this->assertSame($catDiscos->id, $prodComun->catalogo_id);
        $this->assertStringStartsWith('productos/', (string) $prodComun->imagen);
        Storage::disk('public')->assertExists((string) $prodComun->imagen);

        $existenciaComun = Existencia::where('variante_sku_id', $varComun->id)
            ->where('sucursal_id', $sucursal->id)
            ->first();
        $this->assertNotNull($existenciaComun);
        $this->assertSame(8, (int) $existenciaComun->cantidad);

        // Confirmar que los errores no crearon productos inválidos
        $this->assertNull(Producto::where('nombre', 'Producto Inválido')->first());
    }

    public function test_admin_puede_descargar_plantilla_excel_con_3_pestanas_y_importar_xlsx_con_zip(): void
    {
        Storage::fake('public');
        $admin = $this->crearAdmin();

        $catLibros = Catalogo::create(['nombre' => 'Libros', 'tipo' => 'libro', 'activo' => true]);
        $catDiscos = Catalogo::create(['nombre' => 'Discos', 'tipo' => 'disco', 'activo' => true]);
        $catCol = Catalogo::create(['nombre' => 'Coleccionables', 'tipo' => 'coleccionable', 'activo' => true]);

        $sucursal = Sucursal::create([
            'nombre' => 'Sucursal Central Bellas Artes',
            'direccion' => 'Av. Juárez 18',
            'telefono' => '5555128900',
            'activa' => true,
        ]);

        // 1. Descargar plantilla .xlsx y verificar sus 3 pestañas
        $resPlantilla = $this->actingAs($admin)->get(route('admin.productos.plantilla-excel'));
        $resPlantilla->assertOk();
        $xlsxTemp = sys_get_temp_dir().DIRECTORY_SEPARATOR.'plantilla_test_'.uniqid('', true).'.xlsx';
        file_put_contents($xlsxTemp, $resPlantilla->streamedContent());
        $libroDescargado = IOFactory::load($xlsxTemp);
        $this->assertSame(['Libros', 'Discos', 'Coleccionables'], $libroDescargado->getSheetNames());
        @unlink($xlsxTemp);

        // 2. Crear .xlsx con 1 fila en cada pestaña + 1 fila con error en Coleccionables
        $spreadsheet = new Spreadsheet;
        $spreadsheet->removeSheetByIndex(0);

        $hojaLibros = $spreadsheet->createSheet();
        $hojaLibros->setTitle('Libros');
        $hojaLibros->fromArray([
            ['sku', 'titulo', 'autor', 'editorial', 'isbn', 'anio_publicacion', 'paginas', 'genero_literario', 'estado_conservacion', 'sede', 'precio', 'existencias', 'es_pieza_unica', 'es_edicion_especial', 'codigo_coa', 'numero_serie', 'descripcion', 'archivo_imagen'],
            ['XLS-LIB-01', 'Rayuela 1ª Ed.', 'Julio Cortázar', 'Sudamericana', '978-950-07-0099-9', 1963, 600, 'novela', 'VERY FINE (VF)', 'Sucursal Central Bellas Artes', 3200, 4, 1, 1, 'COA-XLS-1', 'SER-XLS-1', 'Novela.', 'libro_xls.jpg'],
        ]);

        $hojaDiscos = $spreadsheet->createSheet();
        $hojaDiscos->setTitle('Discos');
        $hojaDiscos->fromArray([
            ['sku', 'titulo', 'artista', 'disquera_sello', 'formato_discografico', 'anio_lanzamiento', 'genero_musical', 'estado_conservacion', 'sede', 'precio', 'existencias', 'es_pieza_unica', 'es_edicion_especial', 'codigo_coa', 'numero_serie', 'descripcion', 'archivo_imagen'],
            ['XLS-DIS-01', 'Blue Train LP', 'John Coltrane', 'Blue Note', 'vinilo', 1957, 'Jazz', 'MINT (M)', 'Sucursal Central Bellas Artes', 1800, 6, 0, 1, '', '', 'Vinilo 180g.', 'disco_xls.png'],
        ]);

        $hojaCol = $spreadsheet->createSheet();
        $hojaCol->setTitle('Coleccionables');
        $hojaCol->fromArray([
            ['sku', 'titulo', 'fabricante_marca', 'serie', 'subcategoria_coleccionable', 'material', 'estado_conservacion', 'sede', 'precio', 'existencias', 'es_pieza_unica', 'es_edicion_especial', 'codigo_coa', 'numero_serie', 'descripcion', 'archivo_imagen'],
            ['XLS-COL-01', 'Centenario Oro', 'Casa de Moneda', 'Numismática', 'numismatica', 'Oro', 'MINT (M)', 'Sucursal Central Bellas Artes', 55000, 2, 0, 1, '', '', 'Moneda.', ''],
            ['', 'Fila Inválida', 'Marca', 'Serie', 'figuras', 'PVC', 'MINT (M)', 'Sucursal Central Bellas Artes', 500, 1, 0, 0, '', '', '', ''],
        ]);

        $rutaXlsx = sys_get_temp_dir().DIRECTORY_SEPARATOR.'import_test_'.uniqid('', true).'.xlsx';
        (new Xlsx($spreadsheet))->save($rutaXlsx);

        $rutaZip = sys_get_temp_dir().DIRECTORY_SEPARATOR.'zip_xls_'.uniqid('', true).'.zip';
        $zip = new ZipArchive;
        $zip->open($rutaZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $jpegBytes = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
        $pngBytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $zip->addFromString('libro_xls.jpg', $jpegBytes);
        $zip->addFromString('disco_xls.png', $pngBytes);
        $zip->close();

        $response = $this->actingAs($admin)->post(route('admin.productos.importar'), [
            'archivo_csv' => new UploadedFile($rutaXlsx, 'catalogo.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true),
            'archivo_zip' => new UploadedFile($rutaZip, 'imagenes.zip', 'application/zip', null, true),
        ]);

        @unlink($rutaXlsx);
        @unlink($rutaZip);

        $response->assertRedirect(route('admin.productos.index'));
        $response->assertSessionHas('importacion_resumen', function (array $resumen): bool {
            return $resumen['creados'] === 3
                && $resumen['omitidos'] === 1
                && $resumen['imagenes_extraidas'] === 2
                && ($resumen['por_pestana']['Libros']['creados'] ?? 0) === 1
                && ($resumen['por_pestana']['Discos']['creados'] ?? 0) === 1
                && ($resumen['por_pestana']['Coleccionables']['creados'] ?? 0) === 1
                && ($resumen['por_pestana']['Coleccionables']['errores'] ?? 0) === 1;
        });

        $this->assertSame($catLibros->id, VarianteSku::where('sku', 'XLS-LIB-01')->firstOrFail()->producto->catalogo_id);
        $this->assertSame($catDiscos->id, VarianteSku::where('sku', 'XLS-DIS-01')->firstOrFail()->producto->catalogo_id);
        $this->assertSame($catCol->id, VarianteSku::where('sku', 'XLS-COL-01')->firstOrFail()->producto->catalogo_id);
    }
}
