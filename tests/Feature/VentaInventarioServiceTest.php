<?php

namespace Tests\Feature;

use App\Exceptions\StockInsuficienteException;
use App\Models\Carrito;
use App\Models\CarritoItem;
use App\Models\Catalogo;
use App\Models\Cliente;
use App\Models\Existencia;
use App\Models\MovimientoInventario;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\Usuario;
use App\Models\VarianteSku;
use App\Services\InventarioService;
use App\Services\VentaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VentaInventarioServiceTest extends TestCase
{
    use RefreshDatabase;

    protected Sucursal $sucursal;

    protected Catalogo $catalogo;

    protected Producto $producto;

    protected VarianteSku $variante;

    protected Existencia $existencia;

    protected Usuario $usuario;

    protected Cliente $cliente;

    protected Carrito $carrito;

    protected VentaService $ventaService;

    protected InventarioService $inventarioService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->inventarioService = app(InventarioService::class);
        $this->ventaService = app(VentaService::class);

        $this->sucursal = Sucursal::create([
            'nombre' => 'Sucursal Principal',
            'direccion' => 'Av. Central 123',
            'activa' => true,
        ]);

        $this->catalogo = Catalogo::create([
            'nombre' => 'Coleccionables Raros',
            'tipo' => 'coleccionable',
        ]);

        $this->producto = Producto::create([
            'catalogo_id' => $this->catalogo->id,
            'nombre' => 'Figura Stitch Bóveda',
            'descripcion' => 'Figura de edición limitada',
            'precio' => 500.00,
            'activo' => true,
        ]);

        $this->variante = VarianteSku::create([
            'producto_id' => $this->producto->id,
            'sku' => 'STITCH-GLOW-01',
            'descripcion' => 'Edición Luminiscente',
            'precio' => 550.00,
            'activa' => true,
        ]);

        $this->existencia = Existencia::create([
            'variante_sku_id' => $this->variante->id,
            'sucursal_id' => $this->sucursal->id,
            'cantidad' => 10,
            'stock_minimo' => 2,
        ]);

        $this->usuario = Usuario::create([
            'nombre' => 'Vendedor Mostrador',
            'email' => 'vendedor@example.com',
            'password' => 'secret123',
            'activo' => true,
        ]);

        $this->cliente = Cliente::create([
            'usuario_id' => $this->usuario->id,
            'nombre' => 'María',
            'apellidos' => 'López',
            'email' => 'maria@example.com',
            'telefono' => '5551234567',
        ]);

        $this->carrito = Carrito::create([
            'cliente_id' => $this->cliente->id,
        ]);
    }

    public function test_procesar_venta_desde_carrito_descuenta_stock_y_registra_movimiento_y_bitacora(): void
    {
        CarritoItem::create([
            'carrito_id' => $this->carrito->id,
            'variante_sku_id' => $this->variante->id,
            'cantidad' => 3,
        ]);

        $pedido = $this->ventaService->procesarVentaDesdeCarrito(
            carrito: $this->carrito,
            usuario: $this->usuario,
            opciones: [
                'sucursal_id' => $this->sucursal->id,
                'metodo_pago' => 'tarjeta',
            ]
        );

        // 1. Verificación de Pedido
        $this->assertInstanceOf(Pedido::class, $pedido);
        $this->assertEquals('pagado', $pedido->estatus);
        $this->assertEquals(1650.00, $pedido->total); // 3 * 550
        $this->assertDatabaseHas('pedidos', [
            'id' => $pedido->id,
            'cliente_id' => $this->cliente->id,
            'total' => 1650.00,
        ]);

        // 2. Verificación de Detalle de Pedido
        $this->assertDatabaseHas('detalle_pedidos', [
            'pedido_id' => $pedido->id,
            'variante_sku_id' => $this->variante->id,
            'cantidad' => 3,
            'precio_unitario' => 550.00,
        ]);

        // 3. Verificación de Descuento en Existencias (10 - 3 = 7)
        $this->assertEquals(7, $this->existencia->fresh()->cantidad);

        // 4. Verificación de Movimiento de Inventario
        $this->assertDatabaseHas('movimientos_inventario', [
            'existencia_id' => $this->existencia->id,
            'tipo' => 'salida',
            'cantidad' => -3,
            'referencia_type' => Pedido::class,
            'referencia_id' => $pedido->id,
        ]);

        // 5. Verificación de Bitácora
        $this->assertDatabaseHas('bitacoras', [
            'accion' => 'VENTA_PROCESADA',
            'tabla_afectada' => 'pedidos',
            'registro_id' => $pedido->id,
        ]);
        $this->assertDatabaseHas('bitacoras', [
            'accion' => 'STOCK_DESCONTADO',
            'tabla_afectada' => 'existencias',
            'registro_id' => $this->existencia->id,
        ]);

        // 6. Carrito vaciado
        $this->assertCount(0, $this->carrito->items()->get());
    }

    public function test_transaccion_se_revierte_si_stock_es_insuficiente(): void
    {
        // Se piden 15 unidades pero solo existen 10
        CarritoItem::create([
            'carrito_id' => $this->carrito->id,
            'variante_sku_id' => $this->variante->id,
            'cantidad' => 15,
        ]);

        $this->expectException(StockInsuficienteException::class);

        try {
            $this->ventaService->procesarVentaDesdeCarrito(
                carrito: $this->carrito,
                usuario: $this->usuario,
                opciones: ['sucursal_id' => $this->sucursal->id]
            );
        } finally {
            // Verificamos que la transacción atómica revirtió todo:
            // 1. El stock sigue intacto en 10
            $this->assertEquals(10, $this->existencia->fresh()->cantidad);

            // 2. No se creó ningún pedido
            $this->assertEquals(0, Pedido::count());

            // 3. No se registraron movimientos de inventario
            $this->assertEquals(0, MovimientoInventario::count());

            // 4. El carrito no fue vaciado
            $this->assertCount(1, $this->carrito->items()->get());
        }
    }

    public function test_procesar_venta_directa_pos(): void
    {
        $pedido = $this->ventaService->procesarVentaDirecta(
            cliente: $this->cliente,
            items: [
                [
                    'variante_sku_id' => $this->variante->id,
                    'cantidad' => 2,
                    'precio_unitario' => 500.00,
                ],
            ],
            usuario: $this->usuario,
            opciones: [
                'sucursal_id' => $this->sucursal->id,
                'metodo_pago' => 'efectivo',
            ]
        );

        $this->assertInstanceOf(Pedido::class, $pedido);
        $this->assertEquals(1000.00, $pedido->total);
        $this->assertEquals(8, $this->existencia->fresh()->cantidad); // 10 - 2 = 8

        $this->assertDatabaseHas('movimientos_inventario', [
            'existencia_id' => $this->existencia->id,
            'tipo' => 'salida',
            'cantidad' => -2,
        ]);

        $this->assertDatabaseHas('pagos', [
            'pedido_id' => $pedido->id,
            'metodo' => 'efectivo',
            'estatus' => 'aprobado',
        ]);
    }

    public function test_cancelar_pedido_restituye_stock_y_registra_devolucion(): void
    {
        CarritoItem::create([
            'carrito_id' => $this->carrito->id,
            'variante_sku_id' => $this->variante->id,
            'cantidad' => 4,
        ]);

        $pedido = $this->ventaService->procesarVentaDesdeCarrito(
            carrito: $this->carrito,
            usuario: $this->usuario,
            opciones: ['sucursal_id' => $this->sucursal->id]
        );

        $this->assertEquals(6, $this->existencia->fresh()->cantidad); // 10 - 4 = 6

        // Cancelamos el pedido
        $this->ventaService->cancelarPedido(
            pedido: $pedido,
            motivo: 'Cliente solicitó cancelación inmediata',
            sucursalId: $this->sucursal->id,
            usuario: $this->usuario
        );

        // El stock debe haber vuelto a 10
        $this->assertEquals(10, $this->existencia->fresh()->cantidad);
        $this->assertEquals('cancelado', $pedido->fresh()->estatus);

        // Se registra movimiento de inventario de devolución
        $this->assertDatabaseHas('movimientos_inventario', [
            'existencia_id' => $this->existencia->id,
            'tipo' => 'devolucion',
            'cantidad' => 4,
        ]);

        // Se registra en bitácora
        $this->assertDatabaseHas('bitacoras', [
            'accion' => 'PEDIDO_CANCELADO',
            'registro_id' => $pedido->id,
        ]);
    }
}
