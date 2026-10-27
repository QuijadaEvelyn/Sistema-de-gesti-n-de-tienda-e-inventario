<?php

namespace Tests\Feature;

use App\Exceptions\StockInsuficienteException;
use App\Models\Bitacora;
use App\Models\Catalogo;
use App\Models\Existencia;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\Usuario;
use App\Models\VarianteSku;
use App\Services\InventarioService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

/**
 * Tests unitarios de integración para InventarioService.
 * Cubre cada método y cada rama de lógica del servicio.
 */
class InventarioServiceTest extends TestCase
{
    use RefreshDatabase;

    protected InventarioService $servicio;

    protected Sucursal $sucursal;

    protected VarianteSku $variante;

    protected Existencia $existencia;

    protected Usuario $usuario;

    protected function setUp(): void
    {
        parent::setUp();

        $this->servicio = app(InventarioService::class);

        $this->sucursal = Sucursal::create([
            'nombre' => 'Bodega Central',
            'direccion' => 'Calle Ficticia 99',
            'activa' => true,
        ]);

        $catalogo = Catalogo::create([
            'nombre' => 'Catálogo Prueba',
            'tipo' => 'coleccionable',
        ]);

        $producto = Producto::create([
            'catalogo_id' => $catalogo->id,
            'nombre' => 'Figura Prueba',
            'precio' => 100.00,
            'activo' => true,
        ]);

        $this->variante = VarianteSku::create([
            'producto_id' => $producto->id,
            'sku' => 'SKU-TEST-001',
            'precio' => 120.00,
            'activa' => true,
        ]);

        $this->existencia = Existencia::create([
            'variante_sku_id' => $this->variante->id,
            'sucursal_id' => $this->sucursal->id,
            'cantidad' => 20,
            'stock_minimo' => 3,
        ]);

        $this->usuario = Usuario::create([
            'nombre' => 'Admin Prueba',
            'email' => 'admin@test.com',
            'password' => 'secret',
            'activo' => true,
        ]);
    }

    // =========================================================================
    // resolverSucursal()
    // =========================================================================

    public function test_resolver_sucursal_devuelve_la_sucursal_activa_por_id(): void
    {
        $sucursal = $this->servicio->resolverSucursal($this->sucursal->id);

        $this->assertEquals($this->sucursal->id, $sucursal->id);
    }

    public function test_resolver_sucursal_devuelve_la_primera_activa_cuando_no_se_pasa_id(): void
    {
        $sucursal = $this->servicio->resolverSucursal();

        $this->assertEquals($this->sucursal->id, $sucursal->id);
    }

    public function test_resolver_sucursal_lanza_excepcion_si_id_no_existe(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/no existe o no está activa/');

        $this->servicio->resolverSucursal(99999);
    }

    public function test_resolver_sucursal_lanza_excepcion_si_sucursal_esta_inactiva(): void
    {
        $inactiva = Sucursal::create([
            'nombre' => 'Bodega Cerrada',
            'direccion' => 'Calle Sin Salida 0',
            'activa' => false,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/no existe o no está activa/');

        $this->servicio->resolverSucursal($inactiva->id);
    }

    public function test_resolver_sucursal_lanza_excepcion_si_no_hay_ninguna_activa(): void
    {
        // Desactivar la única sucursal registrada
        $this->sucursal->update(['activa' => false]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/No existen sucursales activas/');

        $this->servicio->resolverSucursal();
    }

    // =========================================================================
    // obtenerExistencia()
    // =========================================================================

    public function test_obtener_existencia_devuelve_registro_existente(): void
    {
        $existencia = $this->servicio->obtenerExistencia($this->variante, $this->sucursal->id);

        $this->assertEquals($this->existencia->id, $existencia->id);
        $this->assertEquals(20, $existencia->cantidad);
    }

    public function test_obtener_existencia_acepta_id_entero_de_variante(): void
    {
        $existencia = $this->servicio->obtenerExistencia($this->variante->id, $this->sucursal->id);

        $this->assertEquals($this->existencia->id, $existencia->id);
    }

    public function test_obtener_existencia_crea_registro_con_cero_si_no_existe(): void
    {
        // Nueva variante sin existencia previa
        $catalogo = Catalogo::create(['nombre' => 'Cat Extra', 'tipo' => 'libro']);
        $producto2 = Producto::create([
            'catalogo_id' => $catalogo->id,
            'nombre' => 'Libro Nuevo',
            'precio' => 50.00,
            'activo' => true,
        ]);
        $variante2 = VarianteSku::create([
            'producto_id' => $producto2->id,
            'sku' => 'SKU-NUEVO-002',
            'activa' => true,
        ]);

        $existencia = $this->servicio->obtenerExistencia($variante2, $this->sucursal->id);

        $this->assertNotNull($existencia->id);
        $this->assertEquals(0, $existencia->cantidad);
        $this->assertDatabaseHas('existencias', [
            'variante_sku_id' => $variante2->id,
            'sucursal_id' => $this->sucursal->id,
            'cantidad' => 0,
        ]);
    }

    // =========================================================================
    // verificarStock()
    // =========================================================================

    public function test_verificar_stock_retorna_existencia_si_hay_disponibilidad(): void
    {
        $existencia = $this->servicio->verificarStock($this->variante, 5, $this->sucursal->id);

        $this->assertEquals($this->existencia->id, $existencia->id);
    }

    public function test_verificar_stock_lanza_excepcion_si_cantidad_es_cero(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/mayor a cero/');

        $this->servicio->verificarStock($this->variante, 0, $this->sucursal->id);
    }

    public function test_verificar_stock_lanza_excepcion_si_cantidad_es_negativa(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/mayor a cero/');

        $this->servicio->verificarStock($this->variante, -3, $this->sucursal->id);
    }

    public function test_verificar_stock_lanza_stock_insuficiente_exception_con_datos_correctos(): void
    {
        $this->expectException(StockInsuficienteException::class);

        try {
            $this->servicio->verificarStock($this->variante, 50, $this->sucursal->id);
        } catch (StockInsuficienteException $e) {
            $this->assertEquals(50, $e->solicitado);
            $this->assertEquals(20, $e->disponible);
            $this->assertEquals($this->variante->id, $e->varianteSkuId);
            $this->assertStringContainsString('SKU-TEST-001', $e->getMessage());

            throw $e;
        }
    }

    public function test_verificar_stock_acepta_exactamente_la_cantidad_disponible(): void
    {
        // Verificar exactamente 20 cuando hay 20 no debe lanzar excepción
        $existencia = $this->servicio->verificarStock($this->variante, 20, $this->sucursal->id);

        $this->assertEquals(20, $existencia->cantidad);
    }

    // =========================================================================
    // descontarStock()
    // =========================================================================

    public function test_descontar_stock_reduce_la_cantidad_en_existencias(): void
    {
        $this->servicio->descontarStock(
            variante: $this->variante,
            cantidad: 5,
            sucursalId: $this->sucursal->id,
            usuario: $this->usuario
        );

        $this->assertEquals(15, $this->existencia->fresh()->cantidad);
    }

    public function test_descontar_stock_registra_movimiento_de_tipo_salida(): void
    {
        $movimiento = $this->servicio->descontarStock(
            variante: $this->variante,
            cantidad: 3,
            sucursalId: $this->sucursal->id,
            usuario: $this->usuario,
            motivo: 'Salida por prueba'
        );

        $this->assertInstanceOf(MovimientoInventario::class, $movimiento);
        $this->assertEquals('salida', $movimiento->tipo);
        $this->assertEquals(-3, $movimiento->cantidad);
        $this->assertEquals($this->existencia->id, $movimiento->existencia_id);
        $this->assertEquals($this->usuario->id, $movimiento->usuario_id);
    }

    public function test_descontar_stock_guarda_referencia_polimorfica_correctamente(): void
    {
        // Usar la propia sucursal como referencia morfológica de prueba
        $movimiento = $this->servicio->descontarStock(
            variante: $this->variante,
            cantidad: 2,
            sucursalId: $this->sucursal->id,
            usuario: $this->usuario,
            referencia: $this->sucursal,
            motivo: 'Prueba con referencia'
        );

        $this->assertEquals(Sucursal::class, $movimiento->referencia_type);
        $this->assertEquals($this->sucursal->id, $movimiento->referencia_id);
    }

    public function test_descontar_stock_crea_entrada_en_bitacora_con_accion_correcta(): void
    {
        $this->servicio->descontarStock(
            variante: $this->variante,
            cantidad: 4,
            sucursalId: $this->sucursal->id,
            usuario: $this->usuario
        );

        $this->assertDatabaseHas('bitacoras', [
            'accion' => 'STOCK_DESCONTADO',
            'tabla_afectada' => 'existencias',
            'registro_id' => $this->existencia->id,
            'usuario_id' => $this->usuario->id,
        ]);

        $bitacora = Bitacora::where('accion', 'STOCK_DESCONTADO')->first();
        $detalle = $bitacora->detalle;

        $this->assertEquals(4, $detalle['cantidad_descontada']);
        $this->assertEquals(16, $detalle['stock_restante']);
        $this->assertEquals($this->variante->id, $detalle['variante_sku_id']);
    }

    public function test_descontar_stock_lanza_excepcion_si_no_hay_suficiente(): void
    {
        $this->expectException(StockInsuficienteException::class);

        $this->servicio->descontarStock(
            variante: $this->variante,
            cantidad: 999,
            sucursalId: $this->sucursal->id
        );
    }

    public function test_descontar_stock_no_altera_existencias_si_lanza_excepcion(): void
    {
        try {
            $this->servicio->descontarStock(
                variante: $this->variante,
                cantidad: 999,
                sucursalId: $this->sucursal->id
            );
        } catch (StockInsuficienteException) {
            // La cantidad debe seguir intacta en 20
        }

        $this->assertEquals(20, $this->existencia->fresh()->cantidad);
        $this->assertEquals(0, MovimientoInventario::count());
    }

    // =========================================================================
    // aumentarStock()
    // =========================================================================

    public function test_aumentar_stock_incrementa_la_cantidad_en_existencias(): void
    {
        $this->servicio->aumentarStock(
            variante: $this->variante,
            cantidad: 10,
            sucursalId: $this->sucursal->id,
            usuario: $this->usuario
        );

        $this->assertEquals(30, $this->existencia->fresh()->cantidad);
    }

    public function test_aumentar_stock_registra_movimiento_de_tipo_entrada(): void
    {
        $movimiento = $this->servicio->aumentarStock(
            variante: $this->variante,
            cantidad: 6,
            sucursalId: $this->sucursal->id,
            usuario: $this->usuario,
            motivo: 'Recepción de mercancía'
        );

        $this->assertInstanceOf(MovimientoInventario::class, $movimiento);
        $this->assertEquals('entrada', $movimiento->tipo);
        $this->assertEquals(6, $movimiento->cantidad);
        $this->assertEquals($this->existencia->id, $movimiento->existencia_id);
    }

    public function test_aumentar_stock_acepta_tipo_devolucion(): void
    {
        $movimiento = $this->servicio->aumentarStock(
            variante: $this->variante,
            cantidad: 2,
            sucursalId: $this->sucursal->id,
            tipo: 'devolucion',
            motivo: 'Devolución de cliente'
        );

        $this->assertEquals('devolucion', $movimiento->tipo);
    }

    public function test_aumentar_stock_crea_entrada_en_bitacora(): void
    {
        $this->servicio->aumentarStock(
            variante: $this->variante,
            cantidad: 5,
            sucursalId: $this->sucursal->id,
            usuario: $this->usuario
        );

        $this->assertDatabaseHas('bitacoras', [
            'accion' => 'STOCK_AUMENTADO',
            'tabla_afectada' => 'existencias',
            'registro_id' => $this->existencia->id,
        ]);

        $bitacora = Bitacora::where('accion', 'STOCK_AUMENTADO')->first();
        $detalle = $bitacora->detalle;

        $this->assertEquals(5, $detalle['cantidad_ingresada']);
        $this->assertEquals(25, $detalle['stock_resultante']);
    }

    public function test_aumentar_stock_lanza_excepcion_si_cantidad_es_cero(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/mayor a cero/');

        $this->servicio->aumentarStock(
            variante: $this->variante,
            cantidad: 0,
            sucursalId: $this->sucursal->id
        );
    }

    public function test_aumentar_stock_lanza_excepcion_si_cantidad_es_negativa(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/mayor a cero/');

        $this->servicio->aumentarStock(
            variante: $this->variante,
            cantidad: -5,
            sucursalId: $this->sucursal->id
        );
    }

    public function test_aumentar_stock_crea_existencia_si_no_existe_previamente(): void
    {
        $catalogo = Catalogo::create(['nombre' => 'Cat Nuevo', 'tipo' => 'disco']);
        $producto2 = Producto::create([
            'catalogo_id' => $catalogo->id,
            'nombre' => 'Disco Nuevo',
            'precio' => 80.00,
            'activo' => true,
        ]);
        $variante2 = VarianteSku::create([
            'producto_id' => $producto2->id,
            'sku' => 'SKU-NUEVO-003',
            'activa' => true,
        ]);

        // No hay existencia para esta variante, debe crearse con cantidad 0 + 8
        $this->servicio->aumentarStock(
            variante: $variante2,
            cantidad: 8,
            sucursalId: $this->sucursal->id,
        );

        $this->assertDatabaseHas('existencias', [
            'variante_sku_id' => $variante2->id,
            'sucursal_id' => $this->sucursal->id,
            'cantidad' => 8,
        ]);
    }
}
