<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Catalogo;
use App\Models\Existencia;
use App\Models\PiezaUnica;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Rol;
use App\Models\Sucursal;
use App\Models\Usuario;
use App\Models\VarianteSku;
use Database\Seeders\DatabaseSeeder;
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

    /**
     * MÓDULO 4: Modificación rápida de precios en el panel privado (Administrador y Empleados)
     * e integración con la regla de congelamiento de precio en carritos activos (Productos Comunes vs Piezas Únicas).
     */
    public function test_modificacion_rapida_de_precio_en_panel_privado_refleja_en_tienda_publica_y_respeta_precio_congelado_en_productos_comunes(): void
    {
        $entorno = $this->crearEntornoBase();
        $admin = $this->crearUsuarioConRol('administrador');
        $empleadoAlmacen = $this->crearUsuarioConRol('almacen');
        $empleadoCompras = $this->crearUsuarioConRol('compras');
        $clientePrevio = $this->crearUsuarioConRol('cliente');
        $clienteNuevo = $this->crearUsuarioConRol('cliente');

        // 1. Crear un Producto Común (stock > 1) con precio original $1,500.00 MXN
        $productoComun = Producto::create([
            'catalogo_id' => $entorno['catalogoLibros']->id,
            'nombre' => 'Edición Conmemorativa Poemas Selectos',
            'descripcion' => 'Tiraje regular de catálogo con stock múltiple.',
            'precio' => 1500.00,
            'activo' => true,
        ]);

        $varianteComun = VarianteSku::create([
            'producto_id' => $productoComun->id,
            'sku' => 'LIB-COM-1500',
            'descripcion' => 'MINT (M) · Tiraje Múltiple',
            'precio' => 1500.00,
            'activa' => true,
        ]);

        Existencia::create([
            'variante_sku_id' => $varianteComun->id,
            'sucursal_id' => $entorno['sucursal']->id,
            'cantidad' => 10,
            'stock_minimo' => 1,
        ]);

        // 2. Crear una Pieza Única (stock = 1 + registro en piezas_unicas) con precio original $12,000.00 MXN
        $productoUnico = Producto::create([
            'catalogo_id' => $entorno['catalogoLibros']->id,
            'nombre' => 'Manuscrito Único Firmado 1920',
            'descripcion' => 'Ejemplar único 1/1 numerado.',
            'precio' => 12000.00,
            'activo' => true,
        ]);

        $varianteUnica = VarianteSku::create([
            'producto_id' => $productoUnico->id,
            'sku' => 'LIB-UNI-12000',
            'descripcion' => 'MINT (M) · Pieza Única',
            'precio' => 12000.00,
            'activa' => true,
        ]);

        $existenciaUnica = Existencia::create([
            'variante_sku_id' => $varianteUnica->id,
            'sucursal_id' => $entorno['sucursal']->id,
            'cantidad' => 1,
            'stock_minimo' => 1,
        ]);

        PiezaUnica::create([
            'existencia_id' => $existenciaUnica->id,
            'numero_serie' => 'ARC-UNI-1920-001',
            'estado' => 'disponible',
        ]);

        // 3. El Cliente Previo agrega el Producto Común ($1,500.00) y la Pieza Única ($12,000.00) a su carrito ANTES del cambio de precio
        $this->actingAs($clientePrevio)->post(route('carrito.agregar'), [
            'variante_sku_id' => $varianteComun->id,
            'cantidad' => 2,
        ])->assertRedirect();

        $this->actingAs($clientePrevio)->post(route('carrito.agregar'), [
            'variante_sku_id' => $varianteUnica->id,
            'cantidad' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('carrito_items', [
            'variante_sku_id' => $varianteComun->id,
            'precio_unitario_congelado' => 1500.00,
        ]);

        $this->assertDatabaseHas('carrito_items', [
            'variante_sku_id' => $varianteUnica->id,
            'precio_unitario_congelado' => null,
        ]);

        // 4. Verificar que las vistas privadas (/admin/productos y /admin/inventario) muestran el botón "Editar Precio"
        $this->actingAs($empleadoAlmacen)
            ->get(route('admin.inventario'))
            ->assertOk()
            ->assertSee('Editar Precio')
            ->assertSee('modal_editar_precio_inventario', false);

        $this->actingAs($empleadoCompras)
            ->get(route('admin.productos.index'))
            ->assertOk()
            ->assertSee('Editar Precio')
            ->assertSee('modal_editar_precio', false);

        // 5. Validación: rechaza precios menores a 0.01
        $this->actingAs($empleadoAlmacen)
            ->from(route('admin.inventario'))
            ->patch(route('admin.productos.actualizar-precio', $productoComun), [
                'precio' => 0,
            ])
            ->assertRedirect(route('admin.inventario'))
            ->assertSessionHasErrors(['precio']);

        // 6. Un empleado modifica el precio del Producto Común de $1,500.00 a $2,250.00 MXN
        $this->actingAs($empleadoAlmacen)
            ->from(route('admin.inventario'))
            ->patch(route('admin.productos.actualizar-precio', $productoComun), [
                'precio' => 2250.00,
            ])
            ->assertRedirect(route('admin.inventario'))
            ->assertSessionHas('status');

        $productoComun->refresh();
        $varianteComun->refresh();
        $this->assertEquals(2250.00, (float) $productoComun->precio);
        $this->assertEquals(2250.00, (float) $varianteComun->precio);

        $this->assertDatabaseHas('bitacoras', [
            'accion' => 'ACTUALIZAR_PRECIO_PRODUCTO',
            'tabla_afectada' => 'productos',
            'registro_id' => $productoComun->id,
            'usuario_id' => $empleadoAlmacen->id,
        ]);

        // 7. El Administrador modifica el precio de la Pieza Única de $12,000.00 a $15,500.00 MXN
        $this->actingAs($admin)
            ->from(route('admin.productos.index'))
            ->patch(route('admin.productos.actualizar-precio', $productoUnico), [
                'precio' => 15500.00,
            ])
            ->assertRedirect(route('admin.productos.index'))
            ->assertSessionHas('status');

        $productoUnico->refresh();
        $varianteUnica->refresh();
        $this->assertEquals(15500.00, (float) $productoUnico->precio);
        $this->assertEquals(15500.00, (float) $varianteUnica->precio);

        // 8. Verificar reflejo inmediato en la tienda pública (/catalogo/libros)
        $this->get(route('public.libros'))
            ->assertOk()
            ->assertSee('2,250')
            ->assertSee('15,500');

        // 9. Verificar carrito del Cliente Previo:
        //    - Producto Común mantiene su precio congelado de $1,500.00 (incluso si incrementa cantidad a 3 -> subtotal $4,500.00)
        //    - Pieza Única refleja el nuevo precio vigente de $15,500.00
        $resPatchPrevio = $this->actingAs($clientePrevio)
            ->patchJson(route('carrito.actualizar', $varianteComun->id), [
                'cantidad' => 3,
            ]);

        $resPatchPrevio->assertOk()
            ->assertJsonPath('status', 'success');

        $this->assertEquals(1500.00, (float) $resPatchPrevio->json('carrito.items_regulares.0.precio'));
        $this->assertEquals(1500.00, (float) $resPatchPrevio->json('carrito.items_regulares.0.precio_unitario_congelado'));
        $this->assertEquals(4500.00, (float) $resPatchPrevio->json('carrito.items_regulares.0.subtotal'));
        $this->assertEquals(15500.00, (float) $resPatchPrevio->json('carrito.items_piezas_unicas.0.precio'));

        // El total del carrito del Cliente Previo es 3 * $1,500.00 (común congelado) + 1 * $15,500.00 (pieza única actualizada) = $20,000.00
        $this->assertEquals(20000.00, (float) $resPatchPrevio->json('carrito.total'));

        // 10. Un Cliente Nuevo que agrega el Producto Común DESPUÉS del cambio de precio recibe el nuevo precio de $2,250.00
        $this->flushSession();
        $resNuevoCliente = $this->actingAs($clienteNuevo)
            ->postJson(route('carrito.agregar'), [
                'variante_sku_id' => $varianteComun->id,
                'cantidad' => 1,
            ]);

        $resNuevoCliente->assertOk()
            ->assertJsonPath('status', 'success');
        $this->assertEquals(2250.00, (float) $resNuevoCliente->json('carrito.total'));
    }

    /**
     * MÓDULO 5: Edición de Stock Disponible en la vista de Editar Producto (exclusivo para Productos Comunes,
     * bloqueado en Piezas Únicas / Exclusivas y auditado en `movimientos_inventario`).
     */
    public function test_editar_producto_permite_modificar_stock_en_productos_comunes_y_bloquea_en_piezas_unicas(): void
    {
        $entorno = $this->crearEntornoBase();
        $empleado = $this->crearUsuarioConRol('almacen');

        // 1. Producto Común (stock = 5)
        $productoComun = Producto::create([
            'catalogo_id' => $entorno['catalogoLibros']->id,
            'nombre' => 'Antología Poética Edición Común',
            'descripcion' => 'Tiraje regular editable.',
            'precio' => 650.00,
            'activo' => true,
        ]);

        $varianteComun = VarianteSku::create([
            'producto_id' => $productoComun->id,
            'sku' => 'LIB-EDIT-COM-01',
            'descripcion' => 'MINT (M)',
            'precio' => 650.00,
            'activa' => true,
        ]);

        $existenciaComun = Existencia::create([
            'variante_sku_id' => $varianteComun->id,
            'sucursal_id' => $entorno['sucursal']->id,
            'cantidad' => 5,
            'stock_minimo' => 1,
        ]);

        // 2. Pieza Única / Exclusiva (stock = 1 + registro en piezas_unicas)
        $productoUnico = Producto::create([
            'catalogo_id' => $entorno['catalogoLibros']->id,
            'nombre' => 'Códice Único Numerado 1/1',
            'descripcion' => 'Pieza exclusiva no editable en stock.',
            'precio' => 28000.00,
            'activo' => true,
        ]);

        $varianteUnica = VarianteSku::create([
            'producto_id' => $productoUnico->id,
            'sku' => 'LIB-EDIT-UNI-01',
            'descripcion' => 'MINT (M) · Pieza Única',
            'precio' => 28000.00,
            'activa' => true,
        ]);

        $existenciaUnica = Existencia::create([
            'variante_sku_id' => $varianteUnica->id,
            'sucursal_id' => $entorno['sucursal']->id,
            'cantidad' => 1,
            'stock_minimo' => 1,
        ]);

        PiezaUnica::create([
            'existencia_id' => $existenciaUnica->id,
            'numero_serie' => 'ARC-COD-001',
            'estado' => 'disponible',
        ]);

        // 3. Verificar Vista de Edición para Producto Común: muestra input `name="stock"` editable con valor 5
        $this->actingAs($empleado)
            ->get(route('admin.productos.edit', $productoComun))
            ->assertOk()
            ->assertSee('Stock Disponible (Unidades)')
            ->assertSee('name="stock"', false)
            ->assertDontSee('Pieza Única / Exclusiva — Stock fijo (no editable)');

        // 4. Verificar Vista de Edición para Pieza Única: muestra input deshabilitado y etiqueta de bloqueo
        $this->actingAs($empleado)
            ->get(route('admin.productos.edit', $productoUnico))
            ->assertOk()
            ->assertSee('Stock Disponible (Unidades)')
            ->assertSee('Pieza Única / Exclusiva — Stock fijo (no editable)')
            ->assertDontSee('name="stock"', false);

        // 5. Actualizar Producto Común aumentando stock de 5 a 14 unidades -> actualiza existencias y crea movimiento_inventario
        $this->actingAs($empleado)
            ->put(route('admin.productos.update', $productoComun), [
                'sku' => 'LIB-EDIT-COM-01',
                'precio' => 700.00,
                'stock' => 14,
                'nombre' => 'Antología Poética Edición Común',
                'descripcion' => 'Tiraje regular editable.',
            ])
            ->assertRedirect(route('admin.productos.index'))
            ->assertSessionHas('status');

        $existenciaComun->refresh();
        $this->assertSame(14, (int) $existenciaComun->cantidad);

        $this->assertDatabaseHas('movimientos_inventario', [
            'existencia_id' => $existenciaComun->id,
            'usuario_id' => $empleado->id,
            'cantidad' => 9,
            'motivo' => 'Ajuste manual desde edición de producto',
        ]);

        // 6. Intentar modificar stock de la Pieza Única enviando `stock => 50` en el request -> se ignora el cambio de stock (queda en 1), pero sí actualiza el precio
        $this->actingAs($empleado)
            ->put(route('admin.productos.update', $productoUnico), [
                'sku' => 'LIB-EDIT-UNI-01',
                'precio' => 31000.00,
                'stock' => 50,
                'nombre' => 'Códice Único Numerado 1/1',
                'descripcion' => 'Pieza exclusiva no editable en stock.',
            ])
            ->assertRedirect(route('admin.productos.index'))
            ->assertSessionHas('status');

        $existenciaUnica->refresh();
        $productoUnico->refresh();
        $this->assertSame(1, (int) $existenciaUnica->cantidad);
        $this->assertEquals(31000.00, (float) $productoUnico->precio);
        $this->assertDatabaseMissing('movimientos_inventario', [
            'existencia_id' => $existenciaUnica->id,
            'motivo' => 'Ajuste manual desde edición de producto',
        ]);
    }

    /**
     * MÓDULO 6: Verificación de flujo completo 100% real (Alta de producto en BD sin seeders de relleno ->
     * aparición inmediata en catálogo público -> compra en carrito -> descuento real de stock -> edición).
     */
    public function test_flujo_completo_con_datos_reales_alta_producto_catalogo_publico_compra_y_edicion_sin_seeders_ficticios(): void
    {
        // 1. Ejecutar DatabaseSeeder: solo crea roles, usuarios base, sucursales y catálogos maestros (0 productos ficticios)
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(0, Producto::count());
        $this->assertDatabaseHas('roles', ['nombre' => 'administrador']);
        $this->assertDatabaseHas('roles', ['nombre' => 'compras']);
        $this->assertDatabaseHas('roles', ['nombre' => 'ventas']);
        $this->assertDatabaseHas('roles', ['nombre' => 'almacen']);
        $this->assertDatabaseHas('roles', ['nombre' => 'cliente']);

        $admin = Usuario::where('email', 'admin@archivum.com')->firstOrFail();
        $cliente = Usuario::where('email', 'elena.ramirez@gmail.com')->firstOrFail();
        $catalogoLibros = Catalogo::where('tipo', 'libro')->firstOrFail();
        $sucursal = Sucursal::firstOrFail();

        // 2. Dar de alta un producto real desde el panel administrativo (/admin/productos)
        $resAlta = $this->actingAs($admin)->post(route('admin.productos.store'), [
            'catalogo_id' => $catalogoLibros->id,
            'sku' => 'LIB-REAL-2026-01',
            'nombre' => 'Novela Contemporánea Edición Real',
            'descripcion' => 'Producto creado manualmente en base de datos.',
            'precio' => 450.00,
            'stock_inicial' => 5,
            'sucursal_id' => $sucursal->id,
            'autor' => 'Autor Contemporáneo',
            'editorial' => 'Editorial Moderna',
            'genero' => 'novela',
            'anio_publicacion' => 2026,
            'paginas' => 310,
        ]);

        $resAlta->assertRedirect(route('admin.productos.index'))
            ->assertSessionHas('status');

        $productoReal = Producto::where('nombre', 'Novela Contemporánea Edición Real')->firstOrFail();
        $varianteReal = VarianteSku::where('sku', 'LIB-REAL-2026-01')->firstOrFail();

        $this->assertDatabaseHas('libros', [
            'producto_id' => $productoReal->id,
            'autor' => 'Autor Contemporáneo',
        ]);
        $this->assertDatabaseHas('existencias', [
            'variante_sku_id' => $varianteReal->id,
            'sucursal_id' => $sucursal->id,
            'cantidad' => 5,
        ]);

        // 3. Aparece inmediatamente en la tienda pública (/catalogo/libros y /)
        $this->get(route('public.libros'))
            ->assertOk()
            ->assertSee('Novela Contemporánea Edición Real');

        // 4. Cliente agrega 2 unidades al carrito y confirma su pedido -> el stock real pasa de 5 a 3
        $this->actingAs($cliente)->post(route('carrito.agregar'), [
            'variante_sku_id' => $varianteReal->id,
            'cantidad' => 2,
        ])->assertRedirect();

        $this->assertSame(3, (int) $varianteReal->existencias()->sum('cantidad'));

        $this->actingAs($cliente)->post(route('checkout.pagar'), [
            'nombre_completo' => 'Elena Ramírez',
            'email' => $cliente->email,
            'telefono' => '5511223344',
            'calle' => 'Av. Reforma 100',
            'colonia' => 'Juárez',
            'ciudad' => 'Ciudad de México',
            'estado' => 'CDMX',
            'codigo_postal' => '06600',
            'nombre_titular' => 'Elena Ramírez',
            'numero_tarjeta' => '4111 2222 3333 4444',
            'expiracion' => '12/29',
            'cvv' => '123',
        ])->assertRedirect();

        $this->assertSame(3, (int) $varianteReal->existencias()->sum('cantidad'));
    }
}
