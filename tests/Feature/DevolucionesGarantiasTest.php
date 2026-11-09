<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Catalogo;
use App\Models\Cliente;
use App\Models\DetallePedido;
use App\Models\Devolucion;
use App\Models\Existencia;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Rol;
use App\Models\Sucursal;
use App\Models\Usuario;
use App\Models\VarianteSku;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DevolucionesGarantiasTest extends TestCase
{
    use RefreshDatabase;

    private function crearUsuarioConRol(string $nombreRol): Usuario
    {
        $rol = Rol::firstOrCreate(
            ['nombre' => $nombreRol],
            ['descripcion' => ucfirst($nombreRol)]
        );

        $usuario = Usuario::factory()->create([
            'activo' => true,
        ]);

        $usuario->roles()->syncWithoutDetaching([$rol->id]);

        return $usuario;
    }

    /**
     * @return array{
     *     sucursal: Sucursal,
     *     variante: VarianteSku,
     *     existencia: Existencia,
     *     cliente: Cliente,
     *     pedido: Pedido,
     *     detalle: DetallePedido
     * }
     */
    private function crearEscenarioPedidoEntregado(): array
    {
        $sucursal = Sucursal::create([
            'nombre' => 'Sucursal Central Bellas Artes',
            'direccion' => 'Av. Juárez 18, Centro Histórico',
            'telefono' => '5555128900',
            'activa' => true,
        ]);

        $catalogo = Catalogo::create([
            'nombre' => 'Libros Raros & Incunables',
            'tipo' => 'libro',
            'descripcion' => 'Acervo bibliográfico',
            'activo' => true,
        ]);

        $producto = Producto::create([
            'catalogo_id' => $catalogo->id,
            'nombre' => 'Primera Edición Pedro Páramo (1955)',
            'descripcion' => 'Ejemplar histórico empastado',
            'precio' => 12500.00,
            'activo' => true,
        ]);

        $variante = VarianteSku::create([
            'producto_id' => $producto->id,
            'sku' => 'LIB-RULFO-1955',
            'descripcion' => 'MINT (M) · Primera Impresión',
            'precio' => 12500.00,
            'activa' => true,
        ]);

        // Quedan 2 en bóveda después de haber vendido 2
        $existencia = Existencia::create([
            'variante_sku_id' => $variante->id,
            'sucursal_id' => $sucursal->id,
            'cantidad' => 2,
            'stock_minimo' => 1,
        ]);

        $usuarioCliente = $this->crearUsuarioConRol('cliente');

        $cliente = Cliente::create([
            'usuario_id' => $usuarioCliente->id,
            'nombre' => 'Elena',
            'apellidos' => 'Garro',
            'email' => $usuarioCliente->email,
            'telefono' => '5551112233',
        ]);

        $pedido = Pedido::create([
            'folio' => 'ARC-20261109-GAR01',
            'cliente_id' => $cliente->id,
            'estatus' => 'entregado',
            'subtotal' => 25000.00,
            'descuento' => 0.00,
            'total' => 25000.00,
        ]);

        $detalle = DetallePedido::create([
            'pedido_id' => $pedido->id,
            'variante_sku_id' => $variante->id,
            'cantidad' => 2,
            'precio_unitario' => 12500.00,
            'subtotal' => 25000.00,
        ]);

        return compact('sucursal', 'variante', 'existencia', 'cliente', 'pedido', 'detalle');
    }

    public function test_tabla_devoluciones_contiene_folio_devolucion_y_columnas_de_garantia(): void
    {
        $this->assertTrue(Schema::hasColumn('devoluciones', 'folio_devolucion'));
        $this->assertTrue(Schema::hasColumn('devoluciones', 'etapa_devolucion'));
        $this->assertTrue(Schema::hasColumn('devoluciones', 'comentarios_cliente'));
        $this->assertTrue(Schema::hasColumn('devoluciones', 'evidencias'));
        $this->assertTrue(Schema::hasColumn('devoluciones', 'guia_retorno'));
        $this->assertTrue(Schema::hasColumn('devoluciones', 'destino_inventario'));
        $this->assertTrue(Schema::hasColumn('devoluciones', 'nueva_graduacion'));
        $this->assertTrue(Schema::hasColumn('devoluciones', 'stock_procesado'));
    }

    public function test_cliente_puede_solicitar_devolucion_con_evidencia_y_admin_reintegra_a_stock_con_nueva_graduacion(): void
    {
        Storage::fake('public');

        $escenario = $this->crearEscenarioPedidoEntregado();
        $pedido = $escenario['pedido'];
        $detalle = $escenario['detalle'];
        $existencia = $escenario['existencia'];
        $variante = $escenario['variante'];
        $sucursal = $escenario['sucursal'];
        $usuarioCliente = $escenario['cliente']->usuario;

        // 1. El cliente solicita la devolución de 1 ejemplar adjuntando fotografía y comentarios
        $fotoEvidencia = UploadedFile::fake()->create('lomo-danado.jpg', 120, 'image/jpeg');

        $resSolicitud = $this->actingAs($usuarioCliente)
            ->from(route('cliente.pedidos'))
            ->post(route('cliente.devoluciones.store', $pedido), [
                'detalle_pedido_id' => $detalle->id,
                'cantidad' => 1,
                'motivo' => 'condicion_no_coincide',
                'comentarios_cliente' => 'El lomo presenta desgaste en la esquina superior no descrito en el certificado.',
                'evidencias' => [$fotoEvidencia],
            ]);

        $resSolicitud->assertRedirect(route('cliente.pedidos'));
        $resSolicitud->assertSessionHas('status');

        /** @var Devolucion $devolucion */
        $devolucion = Devolucion::query()->firstOrFail();
        $this->assertNotNull($devolucion->folio_devolucion);
        $this->assertStringStartsWith('DEV-ARC-', $devolucion->folio_devolucion);
        $this->assertSame('solicitada', $devolucion->estatus);
        $this->assertSame('solicitada', $devolucion->etapa_devolucion);
        $this->assertSame(12500.00, (float) $devolucion->monto_reembolso);
        $this->assertCount(1, $devolucion->evidencias);
        $this->assertNotNull($devolucion->evidencia);
        $this->assertStringStartsWith('devoluciones/', $devolucion->evidencia);

        $urlEsperadaEvidencia = asset('storage/'.$devolucion->evidencia);

        // El cliente visualiza la miniatura <img> en su historial de pedidos
        $resHistorialCliente = $this->actingAs($usuarioCliente)->get(route('cliente.pedidos'));
        $resHistorialCliente->assertOk();
        $resHistorialCliente->assertSee($urlEsperadaEvidencia, false);

        // La ruta pública de almacenamiento sirve la imagen sin error 404
        $resImagen = $this->get('/storage/'.$devolucion->evidencia);
        $resImagen->assertOk();

        // 2. El personal de Ventas/Admin revisa la solicitud, visualiza la imagen <img>, autoriza y emite la guía de retorno
        $vendedor = $this->crearUsuarioConRol('ventas');

        $resVistaPanel = $this->actingAs($vendedor)->get(route('ventas.devoluciones'));
        $resVistaPanel->assertOk();
        $resVistaPanel->assertSee($devolucion->folio_devolucion);
        $resVistaPanel->assertSee('Pendiente de revisión');
        $resVistaPanel->assertSee($urlEsperadaEvidencia, false);

        $resDictamen = $this->actingAs($vendedor)->post(route('ventas.devoluciones.dictaminar', $devolucion), [
            'decision' => 'aprobar',
            'dictamen_admin' => 'Evidencia fotográfica válida; procede retorno con custodia.',
            'guia_retorno' => 'RET-ARC-261109-RULFO',
            'instrucciones_retorno' => 'Entregar en cápsula original con guía adherida.',
        ]);

        $resDictamen->assertRedirect();
        $devolucion->refresh();
        $this->assertSame('aprobada', $devolucion->estatus);
        $this->assertSame('aprobada', $devolucion->etapa_devolucion);
        $this->assertSame('RET-ARC-261109-RULFO', $devolucion->guia_retorno);

        // 3. Al recibir físicamente la pieza, se marca que regresa al stock con nueva graduación
        $resRecepcion = $this->actingAs($vendedor)->post(route('ventas.devoluciones.recepcion', $devolucion), [
            'destino_inventario' => 'reintegrar_stock',
            'sucursal_destino_id' => $sucursal->id,
            'nueva_graduacion' => 'VERY GOOD (VG) · Reacondicionado Curatorial',
            'procesar_reembolso' => '1',
        ]);

        $resRecepcion->assertRedirect();
        $devolucion->refresh();

        $this->assertSame('completada', $devolucion->estatus);
        $this->assertSame('reembolso_procesado', $devolucion->etapa_devolucion);
        $this->assertSame('reintegrar_stock', $devolucion->destino_inventario);
        $this->assertTrue($devolucion->stock_procesado);

        // El stock en bóveda subió de 2 a 3 automáticamente y la graduación se actualizó
        $this->assertSame(3, (int) $existencia->fresh()->cantidad);
        $this->assertSame('VERY GOOD (VG) · Reacondicionado Curatorial', $variante->fresh()->descripcion);

        $this->assertDatabaseHas('movimientos_inventario', [
            'existencia_id' => $existencia->id,
            'tipo' => 'devolucion',
            'cantidad' => 1,
            'referencia_id' => $devolucion->id,
        ]);
    }

    public function test_devolucion_defectuosa_enviada_a_merma_no_incrementa_stock_disponible_y_registra_baja(): void
    {
        $escenario = $this->crearEscenarioPedidoEntregado();
        $pedido = $escenario['pedido'];
        $detalle = $escenario['detalle'];
        $existencia = $escenario['existencia'];
        $sucursal = $escenario['sucursal'];
        $admin = $this->crearUsuarioConRol('administrador');

        $this->post(route('cliente.devoluciones.store', $pedido), [
            'detalle_pedido_id' => $detalle->id,
            'cantidad' => 1,
            'motivo' => 'producto_defectuoso',
            'comentarios_cliente' => 'Hojas desprendidas por humedad severa en tránsito.',
        ])->assertRedirect();

        /** @var Devolucion $devolucion */
        $devolucion = Devolucion::query()->firstOrFail();

        $this->actingAs($admin)->post(route('ventas.devoluciones.recepcion', $devolucion), [
            'destino_inventario' => 'merma_baja',
            'sucursal_destino_id' => $sucursal->id,
            'procesar_reembolso' => '1',
        ])->assertRedirect();

        $devolucion->refresh();
        $this->assertSame('merma_baja', $devolucion->destino_inventario);
        $this->assertTrue($devolucion->stock_procesado);

        // El stock disponible para venta permanece en 2 (no se reintegra la pieza dañada)
        $this->assertSame(2, (int) $existencia->fresh()->cantidad);

        // Se registra el movimiento de ajuste/baja auditable
        $this->assertDatabaseHas('movimientos_inventario', [
            'existencia_id' => $existencia->id,
            'tipo' => 'ajuste',
            'cantidad' => -1,
            'referencia_id' => $devolucion->id,
        ]);
    }
}
