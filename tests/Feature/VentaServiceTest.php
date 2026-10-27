<?php

namespace Tests\Feature;

use App\Exceptions\StockInsuficienteException;
use App\Models\Bitacora;
use App\Models\Carrito;
use App\Models\CarritoItem;
use App\Models\Catalogo;
use App\Models\Cliente;
use App\Models\DetallePedido;
use App\Models\Existencia;
use App\Models\MovimientoInventario;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\Usuario;
use App\Models\VarianteSku;
use App\Services\InventarioService;
use App\Services\VentaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

/**
 * Tests unitarios de integración para VentaService.
 * Cubre cada método y cada rama de lógica del servicio.
 */
class VentaServiceTest extends TestCase
{
    use RefreshDatabase;

    protected VentaService $servicio;

    protected InventarioService $inventarioService;

    protected Sucursal $sucursal;

    protected VarianteSku $variante;

    protected VarianteSku $variante2;

    protected Existencia $existencia;

    protected Existencia $existencia2;

    protected Usuario $usuario;

    protected Cliente $cliente;

    protected Carrito $carrito;

    protected function setUp(): void
    {
        parent::setUp();

        $this->inventarioService = app(InventarioService::class);
        $this->servicio = app(VentaService::class);

        $this->sucursal = Sucursal::create([
            'nombre' => 'Tienda Prueba',
            'direccion' => 'Av. Ventas 456',
            'activa' => true,
        ]);

        $catalogo = Catalogo::create([
            'nombre' => 'Catálogo Ventas',
            'tipo' => 'coleccionable',
        ]);

        $producto = Producto::create([
            'catalogo_id' => $catalogo->id,
            'nombre' => 'Figura Limitada A',
            'precio' => 200.00,
            'activo' => true,
        ]);

        $this->variante = VarianteSku::create([
            'producto_id' => $producto->id,
            'sku' => 'FIG-A-001',
            'precio' => 250.00,
            'activa' => true,
        ]);

        $this->existencia = Existencia::create([
            'variante_sku_id' => $this->variante->id,
            'sucursal_id' => $this->sucursal->id,
            'cantidad' => 15,
            'stock_minimo' => 2,
        ]);

        // Segunda variante para pruebas multi-artículo
        $producto2 = Producto::create([
            'catalogo_id' => $catalogo->id,
            'nombre' => 'Figura Limitada B',
            'precio' => 300.00,
            'activo' => true,
        ]);

        $this->variante2 = VarianteSku::create([
            'producto_id' => $producto2->id,
            'sku' => 'FIG-B-002',
            'precio' => null, // usará precio del producto padre
            'activa' => true,
        ]);

        $this->existencia2 = Existencia::create([
            'variante_sku_id' => $this->variante2->id,
            'sucursal_id' => $this->sucursal->id,
            'cantidad' => 5,
            'stock_minimo' => 1,
        ]);

        $this->usuario = Usuario::create([
            'nombre' => 'Vendedor Test',
            'email' => 'vendedor@test.com',
            'password' => 'secret',
            'activo' => true,
        ]);

        $this->cliente = Cliente::create([
            'usuario_id' => $this->usuario->id,
            'nombre' => 'Cliente',
            'apellidos' => 'Prueba',
            'email' => 'cliente@test.com',
        ]);

        $this->carrito = Carrito::create([
            'cliente_id' => $this->cliente->id,
        ]);
    }

    // =========================================================================
    // procesarVentaDesdeCarrito()
    // =========================================================================

    public function test_procesar_venta_desde_carrito_crea_pedido_con_estatus_pagado(): void
    {
        CarritoItem::create([
            'carrito_id' => $this->carrito->id,
            'variante_sku_id' => $this->variante->id,
            'cantidad' => 2,
        ]);

        $pedido = $this->servicio->procesarVentaDesdeCarrito(
            carrito: $this->carrito,
            usuario: $this->usuario,
            opciones: ['sucursal_id' => $this->sucursal->id]
        );

        $this->assertInstanceOf(Pedido::class, $pedido);
        $this->assertEquals('pagado', $pedido->estatus);
        $this->assertStringStartsWith('PED-', $pedido->folio);
    }

    public function test_procesar_venta_calcula_total_usando_precio_de_variante(): void
    {
        CarritoItem::create([
            'carrito_id' => $this->carrito->id,
            'variante_sku_id' => $this->variante->id, // precio variante = 250
            'cantidad' => 3,
        ]);

        $pedido = $this->servicio->procesarVentaDesdeCarrito(
            carrito: $this->carrito,
            opciones: ['sucursal_id' => $this->sucursal->id]
        );

        $this->assertEquals(750.00, $pedido->subtotal);  // 3 * 250
        $this->assertEquals(750.00, $pedido->total);
    }

    public function test_procesar_venta_usa_precio_de_producto_padre_si_variante_no_tiene_precio(): void
    {
        CarritoItem::create([
            'carrito_id' => $this->carrito->id,
            'variante_sku_id' => $this->variante2->id, // variante sin precio → usa producto = 300
            'cantidad' => 2,
        ]);

        $pedido = $this->servicio->procesarVentaDesdeCarrito(
            carrito: $this->carrito,
            opciones: ['sucursal_id' => $this->sucursal->id]
        );

        $this->assertEquals(600.00, $pedido->total); // 2 * 300
    }

    public function test_procesar_venta_aplica_descuento_correctamente(): void
    {
        CarritoItem::create([
            'carrito_id' => $this->carrito->id,
            'variante_sku_id' => $this->variante->id,
            'cantidad' => 4, // 4 * 250 = 1000
        ]);

        $pedido = $this->servicio->procesarVentaDesdeCarrito(
            carrito: $this->carrito,
            opciones: [
                'sucursal_id' => $this->sucursal->id,
                'descuento' => 100.00,
            ]
        );

        $this->assertEquals(1000.00, $pedido->subtotal);
        $this->assertEquals(100.00, $pedido->descuento);
        $this->assertEquals(900.00, $pedido->total);
    }

    public function test_procesar_venta_descuenta_stock_del_inventario(): void
    {
        CarritoItem::create([
            'carrito_id' => $this->carrito->id,
            'variante_sku_id' => $this->variante->id,
            'cantidad' => 5,
        ]);

        $this->servicio->procesarVentaDesdeCarrito(
            carrito: $this->carrito,
            opciones: ['sucursal_id' => $this->sucursal->id]
        );

        $this->assertEquals(10, $this->existencia->fresh()->cantidad); // 15 - 5
    }

    public function test_procesar_venta_registra_movimiento_de_inventario_salida(): void
    {
        CarritoItem::create([
            'carrito_id' => $this->carrito->id,
            'variante_sku_id' => $this->variante->id,
            'cantidad' => 2,
        ]);

        $pedido = $this->servicio->procesarVentaDesdeCarrito(
            carrito: $this->carrito,
            opciones: ['sucursal_id' => $this->sucursal->id]
        );

        $this->assertDatabaseHas('movimientos_inventario', [
            'existencia_id' => $this->existencia->id,
            'tipo' => 'salida',
            'cantidad' => -2,
            'referencia_type' => Pedido::class,
            'referencia_id' => $pedido->id,
        ]);
    }

    public function test_procesar_venta_con_multiples_articulos_descuenta_cada_uno(): void
    {
        CarritoItem::create([
            'carrito_id' => $this->carrito->id,
            'variante_sku_id' => $this->variante->id,
            'cantidad' => 3,
        ]);
        CarritoItem::create([
            'carrito_id' => $this->carrito->id,
            'variante_sku_id' => $this->variante2->id,
            'cantidad' => 2,
        ]);

        $pedido = $this->servicio->procesarVentaDesdeCarrito(
            carrito: $this->carrito,
            opciones: ['sucursal_id' => $this->sucursal->id]
        );

        $this->assertEquals(12, $this->existencia->fresh()->cantidad);   // 15 - 3
        $this->assertEquals(3, $this->existencia2->fresh()->cantidad);   // 5 - 2
        $this->assertEquals(2, $pedido->detalles()->count());
    }

    public function test_procesar_venta_registra_pago_cuando_se_especifica_metodo(): void
    {
        CarritoItem::create([
            'carrito_id' => $this->carrito->id,
            'variante_sku_id' => $this->variante->id,
            'cantidad' => 1,
        ]);

        $pedido = $this->servicio->procesarVentaDesdeCarrito(
            carrito: $this->carrito,
            opciones: [
                'sucursal_id' => $this->sucursal->id,
                'metodo_pago' => 'transferencia',
            ]
        );

        $this->assertDatabaseHas('pagos', [
            'pedido_id' => $pedido->id,
            'metodo' => 'transferencia',
            'estatus' => 'aprobado',
        ]);
    }

    public function test_procesar_venta_no_registra_pago_si_no_se_especifica_metodo(): void
    {
        CarritoItem::create([
            'carrito_id' => $this->carrito->id,
            'variante_sku_id' => $this->variante->id,
            'cantidad' => 1,
        ]);

        $this->servicio->procesarVentaDesdeCarrito(
            carrito: $this->carrito,
            opciones: ['sucursal_id' => $this->sucursal->id]
        );

        $this->assertEquals(0, Pago::count());
    }

    public function test_procesar_venta_vacia_el_carrito(): void
    {
        CarritoItem::create([
            'carrito_id' => $this->carrito->id,
            'variante_sku_id' => $this->variante->id,
            'cantidad' => 1,
        ]);

        $this->servicio->procesarVentaDesdeCarrito(
            carrito: $this->carrito,
            opciones: ['sucursal_id' => $this->sucursal->id]
        );

        $this->assertEquals(0, $this->carrito->items()->count());
    }

    public function test_procesar_venta_registra_bitacora_venta_procesada(): void
    {
        CarritoItem::create([
            'carrito_id' => $this->carrito->id,
            'variante_sku_id' => $this->variante->id,
            'cantidad' => 1,
        ]);

        $pedido = $this->servicio->procesarVentaDesdeCarrito(
            carrito: $this->carrito,
            usuario: $this->usuario,
            opciones: ['sucursal_id' => $this->sucursal->id]
        );

        $this->assertDatabaseHas('bitacoras', [
            'accion' => 'VENTA_PROCESADA',
            'tabla_afectada' => 'pedidos',
            'registro_id' => $pedido->id,
            'usuario_id' => $this->usuario->id,
        ]);
    }

    public function test_procesar_venta_carrito_vacio_lanza_excepcion(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/carrito.*vacío/i');

        $this->servicio->procesarVentaDesdeCarrito(
            carrito: $this->carrito,
            opciones: ['sucursal_id' => $this->sucursal->id]
        );
    }

    public function test_transaccion_se_revierte_completamente_si_stock_insuficiente(): void
    {
        CarritoItem::create([
            'carrito_id' => $this->carrito->id,
            'variante_sku_id' => $this->variante->id,
            'cantidad' => 999, // mucho más de los 15 disponibles
        ]);

        $this->expectException(StockInsuficienteException::class);

        try {
            $this->servicio->procesarVentaDesdeCarrito(
                carrito: $this->carrito,
                opciones: ['sucursal_id' => $this->sucursal->id]
            );
        } finally {
            $this->assertEquals(15, $this->existencia->fresh()->cantidad); // sin cambios
            $this->assertEquals(0, Pedido::count());
            $this->assertEquals(0, MovimientoInventario::count());
            $this->assertEquals(0, DetallePedido::count());
            $this->assertEquals(1, $this->carrito->items()->count()); // carrito intacto
        }
    }

    // =========================================================================
    // procesarVentaDirecta()
    // =========================================================================

    public function test_procesar_venta_directa_crea_pedido_y_descuenta_stock(): void
    {
        $pedido = $this->servicio->procesarVentaDirecta(
            cliente: $this->cliente,
            items: [
                ['variante_sku_id' => $this->variante->id, 'cantidad' => 3],
            ],
            usuario: $this->usuario,
            opciones: ['sucursal_id' => $this->sucursal->id]
        );

        $this->assertInstanceOf(Pedido::class, $pedido);
        $this->assertEquals(12, $this->existencia->fresh()->cantidad); // 15 - 3
        $this->assertEquals(750.00, $pedido->total); // 3 * 250
    }

    public function test_procesar_venta_directa_acepta_precio_unitario_personalizado(): void
    {
        $pedido = $this->servicio->procesarVentaDirecta(
            cliente: $this->cliente,
            items: [
                [
                    'variante_sku_id' => $this->variante->id,
                    'cantidad' => 2,
                    'precio_unitario' => 199.99, // precio especial
                ],
            ],
            opciones: ['sucursal_id' => $this->sucursal->id]
        );

        $this->assertEquals(399.98, $pedido->total); // 2 * 199.99
        $this->assertDatabaseHas('detalle_pedidos', [
            'pedido_id' => $pedido->id,
            'precio_unitario' => 199.99,
        ]);
    }

    public function test_procesar_venta_directa_lista_vacia_lanza_excepcion(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/al menos un artículo/i');

        $this->servicio->procesarVentaDirecta(
            cliente: $this->cliente,
            items: [],
            opciones: ['sucursal_id' => $this->sucursal->id]
        );
    }

    public function test_procesar_venta_directa_registra_bitacora_con_origen_correcto(): void
    {
        $pedido = $this->servicio->procesarVentaDirecta(
            cliente: $this->cliente,
            items: [
                ['variante_sku_id' => $this->variante->id, 'cantidad' => 1],
            ],
            usuario: $this->usuario,
            opciones: ['sucursal_id' => $this->sucursal->id]
        );

        $bitacora = Bitacora::where('accion', 'VENTA_DIRECTA_PROCESADA')
            ->where('registro_id', $pedido->id)
            ->first();

        $this->assertNotNull($bitacora);
        $this->assertEquals('venta_directa_pos', $bitacora->detalle['origen']);
    }

    // =========================================================================
    // confirmarPedido()
    // =========================================================================

    public function test_confirmar_pedido_pendiente_descuenta_stock_y_cambia_estatus(): void
    {
        // Crear pedido pendiente sin stock descontado
        $pedido = Pedido::create([
            'folio' => 'PED-TEST-001',
            'cliente_id' => $this->cliente->id,
            'estatus' => 'pendiente',
            'subtotal' => 500.00,
            'total' => 500.00,
        ]);
        DetallePedido::create([
            'pedido_id' => $pedido->id,
            'variante_sku_id' => $this->variante->id,
            'cantidad' => 2,
            'precio_unitario' => 250.00,
            'subtotal' => 500.00,
        ]);

        $this->servicio->confirmarPedido(
            pedido: $pedido,
            sucursalId: $this->sucursal->id,
            usuario: $this->usuario
        );

        $this->assertEquals('pagado', $pedido->fresh()->estatus);
        $this->assertEquals(13, $this->existencia->fresh()->cantidad); // 15 - 2
        $this->assertDatabaseHas('bitacoras', [
            'accion' => 'PEDIDO_CONFIRMADO',
            'registro_id' => $pedido->id,
        ]);
    }

    public function test_confirmar_pedido_ya_pagado_lo_retorna_sin_duplicar_descuento(): void
    {
        $pedido = Pedido::create([
            'folio' => 'PED-TEST-002',
            'cliente_id' => $this->cliente->id,
            'estatus' => 'pagado',
            'subtotal' => 250.00,
            'total' => 250.00,
        ]);

        $resultado = $this->servicio->confirmarPedido(
            pedido: $pedido,
            sucursalId: $this->sucursal->id
        );

        $this->assertEquals('pagado', $resultado->estatus);
        $this->assertEquals(15, $this->existencia->fresh()->cantidad); // sin cambios
        $this->assertEquals(0, MovimientoInventario::count());
    }

    public function test_confirmar_pedido_cancelado_lanza_excepcion(): void
    {
        $pedido = Pedido::create([
            'folio' => 'PED-TEST-003',
            'cliente_id' => $this->cliente->id,
            'estatus' => 'cancelado',
            'subtotal' => 0,
            'total' => 0,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/fue cancelado/i');

        $this->servicio->confirmarPedido(
            pedido: $pedido,
            sucursalId: $this->sucursal->id
        );
    }

    // =========================================================================
    // cancelarPedido()
    // =========================================================================

    public function test_cancelar_pedido_pagado_restituye_stock(): void
    {
        CarritoItem::create([
            'carrito_id' => $this->carrito->id,
            'variante_sku_id' => $this->variante->id,
            'cantidad' => 5,
        ]);

        $pedido = $this->servicio->procesarVentaDesdeCarrito(
            carrito: $this->carrito,
            opciones: ['sucursal_id' => $this->sucursal->id]
        );
        $this->assertEquals(10, $this->existencia->fresh()->cantidad); // 15 - 5

        $this->servicio->cancelarPedido(
            pedido: $pedido,
            sucursalId: $this->sucursal->id,
            usuario: $this->usuario
        );

        $this->assertEquals(15, $this->existencia->fresh()->cantidad); // stock restituido
        $this->assertEquals('cancelado', $pedido->fresh()->estatus);
    }

    public function test_cancelar_pedido_pagado_registra_movimiento_tipo_devolucion(): void
    {
        CarritoItem::create([
            'carrito_id' => $this->carrito->id,
            'variante_sku_id' => $this->variante->id,
            'cantidad' => 3,
        ]);

        $pedido = $this->servicio->procesarVentaDesdeCarrito(
            carrito: $this->carrito,
            opciones: ['sucursal_id' => $this->sucursal->id]
        );

        $this->servicio->cancelarPedido(
            pedido: $pedido,
            sucursalId: $this->sucursal->id,
            usuario: $this->usuario
        );

        $this->assertDatabaseHas('movimientos_inventario', [
            'existencia_id' => $this->existencia->id,
            'tipo' => 'devolucion',
            'cantidad' => 3,
        ]);
    }

    public function test_cancelar_pedido_pendiente_no_restituye_stock(): void
    {
        // Un pedido pendiente no había descontado stock todavía
        $pedido = Pedido::create([
            'folio' => 'PED-CANCEL-001',
            'cliente_id' => $this->cliente->id,
            'estatus' => 'pendiente',
            'subtotal' => 250.00,
            'total' => 250.00,
        ]);
        DetallePedido::create([
            'pedido_id' => $pedido->id,
            'variante_sku_id' => $this->variante->id,
            'cantidad' => 4,
            'precio_unitario' => 250.00,
            'subtotal' => 1000.00,
        ]);

        $this->servicio->cancelarPedido(
            pedido: $pedido,
            sucursalId: $this->sucursal->id
        );

        $this->assertEquals(15, $this->existencia->fresh()->cantidad); // sin cambios
        $this->assertEquals(0, MovimientoInventario::count()); // no hay devoluciones
    }

    public function test_cancelar_pedido_ya_cancelado_lo_retorna_sin_cambios(): void
    {
        $pedido = Pedido::create([
            'folio' => 'PED-CANCEL-002',
            'cliente_id' => $this->cliente->id,
            'estatus' => 'cancelado',
            'subtotal' => 0,
            'total' => 0,
        ]);

        $resultado = $this->servicio->cancelarPedido($pedido);

        $this->assertEquals('cancelado', $resultado->estatus);
        $this->assertEquals(15, $this->existencia->fresh()->cantidad); // sin cambios
        $this->assertEquals(0, MovimientoInventario::count());
    }

    public function test_cancelar_pedido_registra_bitacora_con_datos_correctos(): void
    {
        CarritoItem::create([
            'carrito_id' => $this->carrito->id,
            'variante_sku_id' => $this->variante->id,
            'cantidad' => 1,
        ]);

        $pedido = $this->servicio->procesarVentaDesdeCarrito(
            carrito: $this->carrito,
            opciones: ['sucursal_id' => $this->sucursal->id]
        );

        $this->servicio->cancelarPedido(
            pedido: $pedido,
            motivo: 'Prueba de cancelación',
            sucursalId: $this->sucursal->id,
            usuario: $this->usuario
        );

        $bitacora = Bitacora::where('accion', 'PEDIDO_CANCELADO')
            ->where('registro_id', $pedido->id)
            ->first();

        $this->assertNotNull($bitacora);
        $this->assertEquals('pagado', $bitacora->detalle['estatus_anterior']);
        $this->assertEquals('Prueba de cancelación', $bitacora->detalle['motivo']);
        $this->assertTrue($bitacora->detalle['stock_restituido']);
    }
}
