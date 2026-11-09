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
use Tests\TestCase;

class ReportesExportacionTest extends TestCase
{
    use RefreshDatabase;

    private function crearAdminAutenticado(): Usuario
    {
        $rolAdmin = Rol::firstOrCreate(
            ['nombre' => 'administrador'],
            ['descripcion' => 'Administrador General']
        );

        $usuario = Usuario::factory()->create([
            'activo' => true,
        ]);

        $usuario->roles()->syncWithoutDetaching([$rolAdmin->id]);

        return $usuario;
    }

    private function sembrarInventarioDePrueba(): array
    {
        $sucursal = Sucursal::create([
            'nombre' => 'Bóveda Central CDMX',
            'direccion' => 'Av. Paseo de la Reforma 222',
            'ciudad' => 'Ciudad de México',
            'telefono' => '5550001122',
            'activa' => true,
        ]);

        $catalogoLibros = Catalogo::create([
            'nombre' => 'Libros Raros & Incunables',
            'tipo' => 'libro',
            'descripcion' => 'Obras impresas históricas',
            'activo' => true,
        ]);

        $catalogoDiscos = Catalogo::create([
            'nombre' => 'Discos & Vinilos Históricos',
            'tipo' => 'disco',
            'descripcion' => 'Prensajes analógicos y ediciones de archivo',
            'activo' => true,
        ]);

        $libro = Producto::create([
            'catalogo_id' => $catalogoLibros->id,
            'nombre' => 'Atlas Astronómico de Nuremberg (1603)',
            'descripcion' => 'Edición príncipe ilustrada',
            'precio' => 45000.00,
            'activo' => true,
        ]);

        $skuLibro = VarianteSku::create([
            'producto_id' => $libro->id,
            'sku' => 'ARC-LIB-1603-N01',
            'descripcion' => 'Folio Imperial · Grado Museum Fine',
            'precio' => 45000.00,
            'activa' => true,
        ]);

        $existenciaLibro = Existencia::create([
            'variante_sku_id' => $skuLibro->id,
            'sucursal_id' => $sucursal->id,
            'cantidad' => 2,
            'stock_minimo' => 1,
        ]);

        PiezaUnica::create([
            'existencia_id' => $existenciaLibro->id,
            'numero_serie' => 'SERIE-1603-001',
            'certificado' => 'COA-ARC-1603-A',
            'notas' => 'Ejemplar certificado con sello de lacre',
            'estado' => 'disponible',
        ]);

        $vinilo = Producto::create([
            'catalogo_id' => $catalogoDiscos->id,
            'nombre' => 'Acetato Maestro de Jazz en Tokio (1961)',
            'descripcion' => 'Prensaje japonés original',
            'precio' => 12500.00,
            'activo' => true,
        ]);

        $skuVinilo = VarianteSku::create([
            'producto_id' => $vinilo->id,
            'sku' => 'ARC-VIN-1961-J09',
            'descripcion' => 'First Pressing Mono 180g · Near Mint',
            'precio' => 12500.00,
            'activa' => true,
        ]);

        $existenciaVinilo = Existencia::create([
            'variante_sku_id' => $skuVinilo->id,
            'sucursal_id' => $sucursal->id,
            'cantidad' => 0,
            'stock_minimo' => 2,
        ]);

        return compact('sucursal', 'libro', 'skuLibro', 'existenciaLibro', 'vinilo', 'skuVinilo', 'existenciaVinilo');
    }

    public function test_vista_de_reportes_calculados_muestra_resumen_del_acervo_e_inventario(): void
    {
        $admin = $this->crearAdminAutenticado();
        $this->sembrarInventarioDePrueba();

        $response = $this->actingAs($admin)->get(route('admin.reportes'));

        $response->assertOk();
        $response->assertSee('Módulo de Reportes & Exportación (CSV y PDF)');
        $response->assertSee('ARC-LIB-1603-N01');
        $response->assertSee('ARC-VIN-1961-J09');
        $response->assertSee('COA-ARC-1603-A');
        $response->assertSee('90,000.00');
        $response->assertSee('Exportar CSV (.csv)');
        $response->assertSee('Descargar PDF Oficial (.pdf)');
    }

    public function test_exportacion_csv_descarga_archivo_con_inventario_y_totales(): void
    {
        $admin = $this->crearAdminAutenticado();
        $this->sembrarInventarioDePrueba();

        $response = $this->actingAs($admin)->get(route('admin.reportes.csv'));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment;', (string) $response->headers->get('Content-Disposition'));

        $contenido = $response->streamedContent();
        $this->assertStringContainsString('ARC-LIB-1603-N01', $contenido);
        $this->assertStringContainsString('ARC-VIN-1961-J09', $contenido);
        $this->assertStringContainsString('COA-ARC-1603-A', $contenido);
        $this->assertStringContainsString('RESUMEN OFICIAL ARCHIVUM', $contenido);
        $this->assertStringContainsString('90000.00', $contenido);
    }

    public function test_exportacion_pdf_descarga_binario_pdf_oficial_de_archivum(): void
    {
        $admin = $this->crearAdminAutenticado();
        $this->sembrarInventarioDePrueba();

        $response = $this->actingAs($admin)->get(route('admin.reportes.pdf'));

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('.pdf', (string) $response->headers->get('Content-Disposition'));

        $binario = (string) $response->getContent();
        $this->assertStringStartsWith('%PDF-1.4', $binario);
        $this->assertStringContainsString('ARCHIVUM', $binario);
        $this->assertStringContainsString('ARC-LIB-1603-N', $binario);
        $this->assertStringContainsString('%%EOF', $binario);
    }
}
