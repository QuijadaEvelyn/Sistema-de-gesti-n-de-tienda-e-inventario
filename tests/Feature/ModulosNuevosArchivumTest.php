<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Catalogo;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Rol;
use App\Models\Sucursal;
use App\Models\Usuario;
use App\Models\VarianteSku;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ModulosNuevosArchivumTest extends TestCase
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

    private function crearEntornoBase(): array
    {
        $sucursal = Sucursal::create([
            'nombre' => 'Sucursal Central Bellas Artes',
            'direccion' => 'Av. Juárez 42, Centro Histórico, CDMX',
            'telefono' => '5555124200',
            'activa' => true,
        ]);

        $catalogoLibros = Catalogo::create([
            'nombre' => 'Libros',
            'tipo' => 'libro',
            'descripcion' => 'Fondo bibliográfico',
            'activo' => true,
        ]);

        $catalogoDiscos = Catalogo::create([
            'nombre' => 'Discos',
            'tipo' => 'disco',
            'descripcion' => 'Fonoteca histórica',
            'activo' => true,
        ]);

        return compact('sucursal', 'catalogoLibros', 'catalogoDiscos');
    }

    /**
     * MÓDULO 1: Descarga de plantilla CSV y validación de autenticación.
     */
    public function test_descargar_plantilla_csv_requiere_rol_autorizado_y_retorna_cabeceras(): void
    {
        $this->get(route('compras.productos.plantilla-csv'))
            ->assertRedirect(route('login'));

        $usuarioCompras = $this->crearUsuarioConRol('compras');

        $response = $this->actingAs($usuarioCompras)
            ->get(route('compras.productos.plantilla-csv'));

        $response->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $this->assertStringContainsString('sku,nombre,catalogo,precio,sucursal,cantidad', $response->streamedContent());
    }

    /**
     * MÓDULO 1: Importación masiva exitosa con creación de registros y bitácora.
     */
    public function test_importacion_masiva_csv_exitosa_crea_productos_existencias_movimientos_y_bitacora(): void
    {
        $entorno = $this->crearEntornoBase();
        $usuarioCompras = $this->crearUsuarioConRol('compras');

        $contenidoCsv = "sku,nombre,catalogo,precio,sucursal,cantidad\n"
                      ."LIB-CSV-100,Ficciones Primera Edicion,Libros,15000.00,Sucursal Central Bellas Artes,4\n"
                      ."DIS-CSV-200,A Love Supreme Coltrane,Discos,8500.50,Sucursal Central Bellas Artes,2\n";

        $archivoCsv = UploadedFile::fake()->createWithContent('importacion_prueba.csv', $contenidoCsv);

        $response = $this->actingAs($usuarioCompras)
            ->post(route('compras.productos.importar-csv'), [
                'archivo_csv' => $archivoCsv,
            ]);

        $response->assertRedirect(route('compras.productos'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('variantes_sku', ['sku' => 'LIB-CSV-100']);
        $this->assertDatabaseHas('variantes_sku', ['sku' => 'DIS-CSV-200']);

        $varianteLibro = VarianteSku::where('sku', 'LIB-CSV-100')->firstOrFail();
        $this->assertDatabaseHas('existencias', [
            'variante_sku_id' => $varianteLibro->id,
            'sucursal_id' => $entorno['sucursal']->id,
            'cantidad' => 4,
        ]);

        $this->assertDatabaseHas('movimientos_inventario', [
            'tipo' => 'entrada',
            'cantidad' => 4,
        ]);

        $this->assertDatabaseHas('bitacoras', [
            'accion' => 'IMPORTACION_CSV',
            'tabla_afectada' => 'productos',
            'usuario_id' => $usuarioCompras->id,
        ]);
    }

    /**
     * MÓDULO 1: Validación de errores en CSV con rollback y detalle en sesión.
     */
    public function test_importacion_masiva_csv_con_errores_valida_filas_hace_rollback_y_reporta_errores(): void
    {
        $this->crearEntornoBase();
        $usuarioCompras = $this->crearUsuarioConRol('compras');

        // Fila 2 tiene catálogo inexistente y fila 3 precio negativo
        $contenidoCsvInvalido = "sku,nombre,catalogo,precio,sucursal,cantidad\n"
                              ."LIB-ERR-1,Obra Invalida,CatalogoFantasma,1200.00,Sucursal Central Bellas Artes,2\n"
                              .",Obra Sin SKU,Libros,-50.00,Sucursal Central Bellas Artes,1\n";

        $archivoCsv = UploadedFile::fake()->createWithContent('invalido.csv', $contenidoCsvInvalido);

        $response = $this->actingAs($usuarioCompras)
            ->from(route('compras.productos'))
            ->post(route('compras.productos.importar-csv'), [
                'archivo_csv' => $archivoCsv,
            ]);

        $response->assertRedirect(route('compras.productos'))
            ->assertSessionHasErrors(['importacion']);

        $this->assertDatabaseMissing('variantes_sku', ['sku' => 'LIB-ERR-1']);
        $this->assertDatabaseMissing('bitacoras', ['accion' => 'IMPORTACION_CSV']);
    }

    /**
     * MÓDULO 2: Acceso público HTTP 200 a Contacto y Horarios.
     */
    public function test_acceso_publico_a_contacto_y_horarios_de_sedes(): void
    {
        $entorno = $this->crearEntornoBase();

        $response = $this->get(route('public.contacto'));

        $response->assertOk()
            ->assertSee('Contacto, Sedes &amp; Horarios de Bóveda', false)
            ->assertSee($entorno['sucursal']->nombre)
            ->assertSee($entorno['sucursal']->direccion)
            ->assertSee('Horario de Bóveda:');
    }

    /**
     * MÓDULO 2: Envío funcional de mensaje de contacto con validación y registro en bitácora.
     */
    public function test_envio_formulario_contacto_publico_valida_y_registra_en_bitacora(): void
    {
        $entorno = $this->crearEntornoBase();

        // 1. Validación fallida cuando campos requeridos están ausentes
        $resInvalido = $this->from(route('public.contacto'))
            ->post(route('public.contacto.enviar'), [
                'nombre' => '',
                'email' => 'correo-invalido',
                'asunto' => '',
                'mensaje' => 'corto',
            ]);

        $resInvalido->assertRedirect(route('public.contacto'))
            ->assertSessionHasErrors(['nombre', 'email', 'asunto', 'mensaje']);

        // 2. Envío exitoso
        $resValido = $this->from(route('public.contacto'))
            ->post(route('public.contacto.enviar'), [
                'nombre' => 'Dra. María Moliner',
                'email' => 'maria.moliner@diccionario.es',
                'telefono' => '5551234567',
                'sucursal_id' => $entorno['sucursal']->id,
                'asunto' => 'Consulta sobre primera edición del Quijote',
                'mensaje' => 'Estimados conservadores de ARCHIVUM, desearía agendar una cita privada para examinar el ejemplar con encuadernación en pergamino.',
            ]);

        $resValido->assertRedirect(route('public.contacto'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('bitacoras', [
            'accion' => 'MENSAJE_CONTACTO_RECIBIDO',
            'tabla_afectada' => 'sucursales',
        ]);
    }

    /**
     * MÓDULO 2: Acceso público HTTP 200 al Aviso de Privacidad.
     */
    public function test_acceso_publico_a_aviso_de_privacidad(): void
    {
        $response = $this->get(route('public.privacidad'));

        $response->assertOk()
            ->assertSee('Aviso de Privacidad Integral')
            ->assertSee('ARCHIVUM S.A. de C.V.')
            ->assertSee('Ejercicio de Derechos ARCO');
    }

    /**
     * MÓDULO 3: Restricción estricta de roles para catálogo de proveedores.
     */
    public function test_restriccion_de_roles_en_modulo_de_proveedores(): void
    {
        // Invitado es redirigido a login
        $this->get(route('compras.proveedores'))
            ->assertRedirect(route('login'));

        // Cliente web es redirigido con advertencia, y vía API/JSON recibe 403 Prohibido
        $cliente = $this->crearUsuarioConRol('cliente');
        $this->actingAs($cliente)
            ->get(route('compras.proveedores'))
            ->assertRedirect(route('public.home'))
            ->assertSessionHas('warning');

        $this->actingAs($cliente)
            ->getJson(route('compras.proveedores'))
            ->assertForbidden();

        // Rol no autorizado (ventas) recibe 403 Prohibido
        $ventas = $this->crearUsuarioConRol('ventas');
        $this->actingAs($ventas)
            ->get(route('compras.proveedores'))
            ->assertForbidden();

        // Rol Compras tiene acceso
        $compras = $this->crearUsuarioConRol('compras');
        $this->actingAs($compras)
            ->get(route('compras.proveedores'))
            ->assertOk()
            ->assertSee('Directorio de Proveedores &amp; Casas Editoriales', false);

        // Rol Administrador tiene acceso
        $admin = $this->crearUsuarioConRol('administrador');
        $this->actingAs($admin)
            ->get(route('compras.proveedores'))
            ->assertOk();
    }

    /**
     * MÓDULO 3: Ciclo CRUD completo y toggle de activación de Proveedores.
     */
    public function test_ciclo_crud_y_toggle_de_proveedores(): void
    {
        $usuarioCompras = $this->crearUsuarioConRol('compras');

        // 1. Alta de Proveedor
        $responseStore = $this->actingAs($usuarioCompras)
            ->post(route('compras.proveedores.store'), [
                'nombre' => 'Fondo de Cultura Económica',
                'razon_social' => 'FCE S.A. de C.V.',
                'rfc' => 'FCE340915ABC',
                'contacto_nombre' => 'Lic. Daniel Goldin',
                'email' => 'editorial@fce.com.mx',
                'telefono' => '+52 55 5227 4600',
                'direccion' => 'Carretera Picacho Ajusco 227, CDMX',
            ]);

        $responseStore->assertRedirect(route('compras.proveedores'))
            ->assertSessionHas('status');

        $proveedor = Proveedor::where('rfc', 'FCE340915ABC')->firstOrFail();
        $this->assertTrue($proveedor->activo);

        $this->assertDatabaseHas('bitacoras', [
            'accion' => 'PROVEEDOR_CREADO',
            'tabla_afectada' => 'proveedores',
            'registro_id' => $proveedor->id,
        ]);

        // 2. Edición / Actualización de Proveedor
        $responseUpdate = $this->actingAs($usuarioCompras)
            ->put(route('compras.proveedores.update', $proveedor), [
                'nombre' => 'FCE México & Filiales',
                'razon_social' => 'FCE S.A. de C.V.',
                'rfc' => 'FCE340915ABC',
                'contacto_nombre' => 'Dr. Paco Ignacio Taibo II',
                'email' => 'contacto@fce.com.mx',
                'telefono' => '+52 55 5227 4699',
                'direccion' => 'Picacho Ajusco 227, Tlalpan, CDMX',
            ]);

        $responseUpdate->assertRedirect(route('compras.proveedores'))
            ->assertSessionHas('status');

        $proveedor->refresh();
        $this->assertEquals('FCE México & Filiales', $proveedor->nombre);
        $this->assertEquals('Dr. Paco Ignacio Taibo II', $proveedor->contacto_nombre);

        $this->assertDatabaseHas('bitacoras', [
            'accion' => 'PROVEEDOR_ACTUALIZADO',
            'tabla_afectada' => 'proveedores',
            'registro_id' => $proveedor->id,
        ]);

        // 3. Toggle de Activación / Desactivación Lógica
        $responseToggle = $this->actingAs($usuarioCompras)
            ->patch(route('compras.proveedores.toggle', $proveedor));

        $responseToggle->assertRedirect(route('compras.proveedores'))
            ->assertSessionHas('status');

        $proveedor->refresh();
        $this->assertFalse($proveedor->activo);

        $this->assertDatabaseHas('bitacoras', [
            'accion' => 'PROVEEDOR_ESTADO_CAMBIADO',
            'tabla_afectada' => 'proveedores',
            'registro_id' => $proveedor->id,
        ]);

        // 4. Reactivación
        $this->actingAs($usuarioCompras)
            ->patch(route('compras.proveedores.toggle', $proveedor));
        $proveedor->refresh();
        $this->assertTrue($proveedor->activo);
    }

    /**
     * MÓDULO 3: Recepción de mercancía asociando un proveedor registrado.
     */
    public function test_recepcion_de_mercancia_admite_proveedor_registrado_y_asigna_proveedor_id(): void
    {
        $entorno = $this->crearEntornoBase();
        $usuarioCompras = $this->crearUsuarioConRol('compras');

        $proveedor = Proveedor::create([
            'nombre' => 'Editorial Sudamericana',
            'razon_social' => 'Sudamericana S.A.',
            'rfc' => 'SUD19670101',
            'activo' => true,
        ]);

        $producto = Producto::create([
            'catalogo_id' => $entorno['catalogoLibros']->id,
            'nombre' => 'Rayuela (Edición Príncipe)',
            'precio' => 38000.00,
            'activo' => true,
        ]);

        $variante = VarianteSku::create([
            'producto_id' => $producto->id,
            'sku' => 'LIB-RAY-001',
            'descripcion' => 'Ejemplar en rama',
            'precio' => 38000.00,
            'activa' => true,
        ]);

        $response = $this->actingAs($usuarioCompras)
            ->post(route('compras.ordenes.recepcionar'), [
                'proveedor_id' => $proveedor->id,
                'sucursal_id' => $entorno['sucursal']->id,
                'variante_sku_id' => $variante->id,
                'cantidad' => 5,
            ]);

        $response->assertRedirect(route('compras.ordenes'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('movimientos_inventario', [
            'tipo' => 'entrada',
            'cantidad' => 5,
            'proveedor_id' => $proveedor->id,
        ]);

        $this->assertDatabaseHas('bitacoras', [
            'accion' => 'STOCK_PROVEEDOR_RECEPCIONADO',
            'tabla_afectada' => 'movimientos_inventario',
        ]);
    }
}
