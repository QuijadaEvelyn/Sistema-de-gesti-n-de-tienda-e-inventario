<?php

namespace Tests\Feature;

use App\Events\PedidoPagado;
use App\Exceptions\ReservaExpiradaException;
use App\Exceptions\StockInsuficienteException;
use App\Models\Carrito;
use App\Models\CarritoItem;
use App\Models\Catalogo;
use App\Models\Cliente;
use App\Models\ClienteFrecuente;
use App\Models\ClientePromocion;
use App\Models\DetallePago;
use App\Models\DetallePedido;
use App\Models\Envio;
use App\Models\Existencia;
use App\Models\HistorialEnvio;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\PiezaUnica;
use App\Models\Producto;
use App\Models\Promocion;
use App\Models\ReservaStock;
use App\Models\Sucursal;
use App\Models\Usuario;
use App\Models\VarianteSku;
use App\Services\CarritoService;
use App\Services\EnvioService;
use App\Services\NotificacionService;
use App\Services\OrdenCompraProveedorService;
use App\Services\PromocionDescuentoService;
use App\Services\ReporteInventarioService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ServiciosNegocioTest extends TestCase
{
    use RefreshDatabase;

    protected Sucursal $sucursal;

    protected Catalogo $catalogo;

    protected Producto $producto;

    protected VarianteSku $variante;

    protected Existencia $existencia;

    protected Cliente $cliente;

    protected Carrito $carrito;

    protected Usuario $usuario;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sucursal = Sucursal::create([
            'nombre' => 'Matriz Centro',
            'direccion' => 'Av. Principal 123',
            'telefono' => '5551234567',
            'activa' => true,
        ]);

        $this->catalogo = Catalogo::create([
            'nombre' => 'Libros Generales',
            'tipo' => 'libro',
            'descripcion' => 'Catalogo de prueba',
            'activo' => true,
        ]);

        $this->producto = Producto::create([
            'catalogo_id' => $this->catalogo->id,
            'nombre' => 'El Gran Libro',
            'descripcion' => 'Edicion de lujo',
            'precio' => 250.00,
            'activo' => true,
        ]);

        $this->variante = VarianteSku::create([
            'producto_id' => $this->producto->id,
            'sku' => 'LIB-001',
            'descripcion' => 'Tapa dura',
            'precio' => 300.00,
            'activa' => true,
        ]);

        $this->existencia = Existencia::create([
            'variante_sku_id' => $this->variante->id,
            'sucursal_id' => $this->sucursal->id,
            'cantidad' => 20,
            'stock_minimo' => 5,
        ]);

        $this->usuario = Usuario::create([
            'nombre' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('secret123'),
            'activo' => true,
        ]);

        $this->cliente = Cliente::create([
            'nombre' => 'Juan',
            'apellidos' => 'Perez',
            'email' => 'juan@test.com',
            'telefono' => '5559876543',
        ]);

        $this->carrito = Carrito::create([
            'cliente_id' => $this->cliente->id,
        ]);
    }

    public function test_carrito_service_agregar_remover_y_calcular_totales(): void
    {
        /** @var CarritoService $carritoService */
        $carritoService = app(CarritoService::class);

        $item = $carritoService->agregarItem($this->carrito, $this->variante->id, 2, $this->sucursal->id);
        $this->assertInstanceOf(CarritoItem::class, $item);
        $this->assertEquals(2, $item->cantidad);

        $totales = $carritoService->calcularTotales($this->carrito);
        $this->assertEquals(600.00, $totales['subtotal']);
        $this->assertEquals(0.00, $totales['envio']); // Gratis porque subtotal >= 500
        $this->assertEquals(600.00, $totales['total']);

        $removido = $carritoService->removerItem($this->carrito, $this->variante->id);
        $this->assertTrue($removido);
        $this->assertDatabaseMissing('carrito_items', ['id' => $item->id]);
    }

    public function test_carrito_service_valida_stock_insuficiente(): void
    {
        /** @var CarritoService $carritoService */
        $carritoService = app(CarritoService::class);

        $this->expectException(StockInsuficienteException::class);
        $carritoService->agregarItem($this->carrito, $this->variante->id, 50, $this->sucursal->id);
    }

    public function test_promocion_descuento_service_validacion_y_calculo(): void
    {
        /** @var PromocionDescuentoService $promoService */
        $promoService = app(PromocionDescuentoService::class);

        $promocion = Promocion::create([
            'nombre' => 'Descuento 15%',
            'descripcion' => 'Descuento de bienvenida',
            'tipo_descuento' => 'porcentaje',
            'valor' => 15.00,
            'inicia_en' => Carbon::yesterday(),
            'termina_en' => Carbon::tomorrow(),
            'activa' => true,
        ]);

        $validada = $promoService->validarPromocion($promocion->id);
        $this->assertEquals($promocion->id, $validada->id);

        $descuento = $promoService->calcularMontoDescuento($promocion, 500.00);
        $this->assertEquals(75.00, $descuento);
    }

    public function test_promocion_descuento_service_cliente_frecuente(): void
    {
        /** @var PromocionDescuentoService $promoService */
        $promoService = app(PromocionDescuentoService::class);

        $clienteFrecuente = ClienteFrecuente::create([
            'cliente_id' => $this->cliente->id,
            'desde' => now(),
            'puntos' => 100,
        ]);

        $promocion = Promocion::create([
            'nombre' => 'Cupón 50 pesos',
            'tipo_descuento' => 'monto',
            'valor' => 50.00,
            'inicia_en' => Carbon::yesterday(),
            'termina_en' => Carbon::tomorrow(),
            'activa' => true,
        ]);

        ClientePromocion::create([
            'cliente_frecuente_id' => $clienteFrecuente->cliente_id,
            'promocion_id' => $promocion->id,
            'recibida_en' => now(),
            'usada_en' => null,
        ]);

        $activas = $promoService->promocionesActivasParaCliente($clienteFrecuente->cliente_id);
        $this->assertCount(1, $activas);

        $promoService->marcarComoUsada($promocion->id, $clienteFrecuente->cliente_id);

        $activasDespues = $promoService->promocionesActivasParaCliente($clienteFrecuente->cliente_id);
        $this->assertCount(0, $activasDespues);
    }

    public function test_notificacion_service_emite_logs_sin_errores(): void
    {
        /** @var NotificacionService $notificacionService */
        $notificacionService = app(NotificacionService::class);

        $pedido = Pedido::create([
            'folio' => 'PED-TEST-1',
            'cliente_id' => $this->cliente->id,
            'estatus' => 'pagado',
            'subtotal' => 300.00,
            'descuento' => 0.00,
            'total' => 300.00,
        ]);

        $notificacionService->enviarConfirmacionPedido($pedido);
        $notificacionService->enviarFichaOxxo($pedido, '141234567890123456', Carbon::now()->addDays(3));

        $this->assertTrue(true);
    }

    public function test_orden_compra_proveedor_recepciona_stock(): void
    {
        /** @var OrdenCompraProveedorService $proveedorService */
        $proveedorService = app(OrdenCompraProveedorService::class);

        $stockInicial = $this->existencia->cantidad;

        $movimientos = $proveedorService->recepcionarStock([
            ['variante_sku_id' => $this->variante->id, 'cantidad' => 10],
        ], $this->sucursal->id, 'Entrada de lote semanal', $this->usuario);

        $this->assertCount(1, $movimientos);
        $this->assertDatabaseHas('existencias', [
            'id' => $this->existencia->id,
            'cantidad' => $stockInicial + 10,
        ]);
    }

    public function test_reporte_inventario_valoracion_y_rotacion(): void
    {
        /** @var ReporteInventarioService $reporteService */
        $reporteService = app(ReporteInventarioService::class);

        $valoracion = $reporteService->valoracionPorSucursal($this->sucursal->id);
        $this->assertNotEmpty($valoracion);
        $this->assertEquals($this->sucursal->id, $valoracion[0]['sucursal_id']);
        $this->assertEquals(6000.00, $valoracion[0]['valor_total_inventario']); // 20 * 300.00

        PiezaUnica::create([
            'existencia_id' => $this->existencia->id,
            'numero_serie' => 'SN-1001',
            'estado' => 'disponible',
        ]);
        PiezaUnica::create([
            'existencia_id' => $this->existencia->id,
            'numero_serie' => 'SN-1002',
            'estado' => 'vendida',
        ]);

        $rotacion = $reporteService->rotacionPiezasUnicas($this->sucursal->id);
        $this->assertEquals(2, $rotacion['total_piezas']);
        $this->assertEquals(1, $rotacion['total_disponibles']);
        $this->assertEquals(1, $rotacion['total_vendidas']);
        $this->assertEquals(50.0, $rotacion['tasa_rotacion_porcentaje']);
    }

    public function test_comando_liberar_reservas_y_pedidos_expirados(): void
    {
        $pedido = Pedido::create([
            'folio' => 'PED-EXPIRADO-1',
            'cliente_id' => $this->cliente->id,
            'estatus' => 'pendiente',
            'subtotal' => 300.00,
            'descuento' => 0.00,
            'total' => 300.00,
        ]);

        $pago = Pago::create([
            'pedido_id' => $pedido->id,
            'metodo' => 'efectivo',
            'monto' => 300.00,
            'estatus' => 'pendiente',
        ]);

        DetallePago::create([
            'pago_id' => $pago->id,
            'referencia_externa' => '149999999999999999',
            'respuesta_pasarela' => [
                'expira_en' => Carbon::yesterday()->toIso8601String(),
            ],
        ]);

        $reserva = ReservaStock::create([
            'existencia_id' => $this->existencia->id,
            'pedido_id' => $pedido->id,
            'cantidad' => 2,
            'expira_en' => Carbon::yesterday(),
            'estado' => 'activa',
        ]);

        $this->artisan('app:limpiar-reservas-y-pedidos-expirados')
            ->assertExitCode(0);

        $this->assertDatabaseHas('pagos', [
            'id' => $pago->id,
            'estatus' => 'rechazado',
        ]);

        $this->assertDatabaseHas('pedidos', [
            'id' => $pedido->id,
            'estatus' => 'cancelado',
        ]);
    }

    public function test_excepciones_personalizadas_render(): void
    {
        $excStock = new StockInsuficienteException('Sin stock', 422, null, 1, 5, 2);
        $resStock = $excStock->render(Request::create('/api/checkout', 'POST'));
        $this->assertEquals(422, $resStock->getStatusCode());
        $this->assertEquals('STOCK_INSUFICIENTE', $resStock->getData(true)['error']);

        $excReserva = new ReservaExpiradaException('Expiro', 409, null, 10, 20);
        $resReserva = $excReserva->render(Request::create('/api/checkout', 'POST'));
        $this->assertEquals(409, $resReserva->getStatusCode());
        $this->assertEquals('RESERVA_EXPIRADA', $resReserva->getData(true)['error']);
    }

    public function test_evento_pedido_pagado_dispara_listeners(): void
    {
        $pedido = Pedido::create([
            'folio' => 'PED-EVENT-1',
            'cliente_id' => $this->cliente->id,
            'estatus' => 'pagado',
            'subtotal' => 300.00,
            'descuento' => 0.00,
            'total' => 300.00,
        ]);

        DetallePedido::create([
            'pedido_id' => $pedido->id,
            'variante_sku_id' => $this->variante->id,
            'cantidad' => 1,
            'precio_unitario' => 300.00,
            'subtotal' => 300.00,
        ]);

        PedidoPagado::dispatch($pedido);

        $this->assertDatabaseHas('envios', [
            'pedido_id' => $pedido->id,
            'estatus' => 'preparando',
        ]);
    }

    public function test_historial_envios_y_ajustes_modelo_envio(): void
    {
        $pedido = Pedido::create([
            'folio' => 'PED-ENVIO-TRACK',
            'cliente_id' => $this->cliente->id,
            'estatus' => 'pagado',
            'subtotal' => 300.00,
            'descuento' => 0.00,
            'total' => 300.00,
        ]);

        $envio = Envio::create([
            'pedido_id' => $pedido->id,
            'sucursal_id' => $this->sucursal->id,
            'paqueteria' => 'DHL Express',
            'numero_guia' => 'GUA-TEST-9988',
            'estado_actual' => 'en_preparacion',
            'fecha_estimada_entrega' => now()->addDays(2),
            'estatus' => 'preparando',
        ]);

        $this->assertInstanceOf(Carbon::class, $envio->fecha_estimada_entrega);
        $this->assertEquals('en_preparacion', $envio->estado_actual);

        $historial = HistorialEnvio::create([
            'envio_id' => $envio->id,
            'estado' => 'en_transito',
            'ubicacion' => 'Centro de clasificacion Norte',
            'descripcion' => 'Paquete recibido en instalacion de paqueteria',
            'created_at' => now(),
        ]);

        $this->assertInstanceOf(Envio::class, $historial->envio);
        $this->assertEquals($envio->id, $historial->envio->id);

        $this->assertCount(1, $envio->fresh()->historial);
        $this->assertEquals('en_transito', $envio->historial->first()->estado);
    }

    public function test_ajustes_modelo_pago_campos_y_casts(): void
    {
        $pedido = Pedido::create([
            'folio' => 'PED-PAGO-TEST',
            'cliente_id' => $this->cliente->id,
            'estatus' => 'pendiente',
            'subtotal' => 500.00,
            'descuento' => 0.00,
            'total' => 500.00,
        ]);

        $expiraAt = Carbon::now()->addDays(3);

        $pagoOxxo = Pago::create([
            'pedido_id' => $pedido->id,
            'metodo' => 'efectivo',
            'metodo_pago' => 'oxxo',
            'monto' => 500.00,
            'codigo_oxxo' => '149876543210987654',
            'referencia_bancaria' => null,
            'expira_at' => $expiraAt,
            'comprobante_url' => null,
            'estatus' => 'pendiente',
        ]);

        $this->assertInstanceOf(Carbon::class, $pagoOxxo->expira_at);
        $this->assertEquals('oxxo', $pagoOxxo->metodo_pago);
        $this->assertEquals('149876543210987654', $pagoOxxo->codigo_oxxo);

        $pagoTransf = Pago::create([
            'pedido_id' => $pedido->id,
            'metodo' => 'transferencia',
            'metodo_pago' => 'transferencia',
            'monto' => 500.00,
            'referencia_bancaria' => 'SPEI-778899',
            'comprobante_url' => 'https://almacen.test/comprobantes/spei-778899.pdf',
            'estatus' => 'pendiente',
        ]);

        $this->assertEquals('transferencia', $pagoTransf->metodo_pago);
        $this->assertEquals('SPEI-778899', $pagoTransf->referencia_bancaria);
        $this->assertEquals('https://almacen.test/comprobantes/spei-778899.pdf', $pagoTransf->comprobante_url);
    }

    public function test_envio_service_actualizar_ubicacion_registra_en_historial_envios(): void
    {
        /** @var EnvioService $envioService */
        $envioService = app(EnvioService::class);

        $pedido = Pedido::create([
            'folio' => 'PED-HISTORIAL-ENV',
            'cliente_id' => $this->cliente->id,
            'estatus' => 'pagado',
            'subtotal' => 300.00,
            'descuento' => 0.00,
            'total' => 300.00,
        ]);

        DetallePedido::create([
            'pedido_id' => $pedido->id,
            'variante_sku_id' => $this->variante->id,
            'cantidad' => 1,
            'precio_unitario' => 300.00,
            'subtotal' => 300.00,
        ]);

        $envio = $envioService->crearEnvio($pedido, 'Av. Siempreviva 742');

        $envioActualizado = $envioService->actualizarUbicacionRastreo(
            $envio->id,
            'en_camino',
            'En ruta de entrega local',
            'El repartidor se encuentra a 3 paradas de su domicilio.'
        );

        $this->assertEquals('en_camino', $envioActualizado->estado_actual);
        $this->assertDatabaseHas('historial_envios', [
            'envio_id' => $envio->id,
            'estado' => 'en_camino',
            'ubicacion' => 'En ruta de entrega local',
        ]);
    }
}
