<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Catalogo;
use App\Models\Existencia;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Rol;
use App\Models\Sucursal;
use App\Models\Usuario;
use App\Models\VarianteSku;
use App\Services\SucursalContextoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardFiltradoSucursalTest extends TestCase
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
     *     bellasArtes: Sucursal,
     *     coyoacan: Sucursal,
     *     almacen: Sucursal,
     *     skuBellasArtes: VarianteSku,
     *     skuCoyoacan: VarianteSku
     * }
     */
    private function sembrarEscenarioMultisucursal(): array
    {
        $bellasArtes = Sucursal::create([
            'nombre' => 'Sucursal Central Bellas Artes',
            'direccion' => 'Av. Juárez 18, Centro Histórico',
            'telefono' => '5555128900',
            'activa' => true,
        ]);

        $coyoacan = Sucursal::create([
            'nombre' => 'Sucursal Coyoacán',
            'direccion' => 'Jardín Centenario 14, Coyoacán',
            'telefono' => '5556584420',
            'activa' => true,
        ]);

        $almacen = Sucursal::create([
            'nombre' => 'Almacén General',
            'direccion' => 'Calz. Vallejo 1020, Azcapotzalco',
            'telefono' => '5553689100',
            'activa' => true,
        ]);

        $catalogo = Catalogo::create([
            'nombre' => 'Libros Raros & Incunables',
            'tipo' => 'libro',
            'descripcion' => 'Acervo bibliográfico',
            'activo' => true,
        ]);

        $prodBellasArtes = Producto::create([
            'catalogo_id' => $catalogo->id,
            'nombre' => 'Incunable Exclusivo Bellas Artes (1540)',
            'descripcion' => 'Pieza custodiada en Bellas Artes',
            'precio' => 20000.00,
            'activo' => true,
        ]);

        $skuBellasArtes = VarianteSku::create([
            'producto_id' => $prodBellasArtes->id,
            'sku' => 'SKU-BELLAS-1540',
            'descripcion' => 'MINT (M) · Bellas Artes',
            'precio' => 20000.00,
            'activa' => true,
        ]);

        Existencia::create([
            'variante_sku_id' => $skuBellasArtes->id,
            'sucursal_id' => $bellasArtes->id,
            'cantidad' => 5,
            'stock_minimo' => 1,
        ]);

        $prodCoyoacan = Producto::create([
            'catalogo_id' => $catalogo->id,
            'nombre' => 'Manuscrito Exclusivo Coyoacán (1925)',
            'descripcion' => 'Pieza custodiada únicamente en Coyoacán',
            'precio' => 15000.00,
            'activo' => true,
        ]);

        $skuCoyoacan = VarianteSku::create([
            'producto_id' => $prodCoyoacan->id,
            'sku' => 'SKU-COYOACAN-1925',
            'descripcion' => 'VERY FINE (VF) · Coyoacán',
            'precio' => 15000.00,
            'activa' => true,
        ]);

        Existencia::create([
            'variante_sku_id' => $skuCoyoacan->id,
            'sucursal_id' => $coyoacan->id,
            'cantidad' => 3,
            'stock_minimo' => 1,
        ]);

        return compact('bellasArtes', 'coyoacan', 'almacen', 'skuBellasArtes', 'skuCoyoacan');
    }

    public function test_selector_de_sucursales_actualiza_contexto_global_de_sesion_en_vista_publica_y_admin(): void
    {
        $escenario = $this->sembrarEscenarioMultisucursal();
        $coyoacan = $escenario['coyoacan'];

        // 1. Cambio síncrono desde la tienda pública
        $responsePublico = $this->from(route('public.home'))
            ->post(route('sucursal.cambiar'), [
                'sucursal_id' => $coyoacan->id,
            ]);

        $responsePublico->assertRedirect(route('public.home'));
        $this->assertSame($coyoacan->id, session(SucursalContextoService::SESSION_SUCURSAL_KEY));

        // 2. La vista pública refleja la sucursal seleccionada en el selector
        $vistaPublica = $this->get(route('public.home'));
        $vistaPublica->assertOk();
        $vistaPublica->assertSee('public-branch-select', false);
        $vistaPublica->assertSee('Sucursal Coyoacán');
    }

    public function test_dashboard_filtra_estrictamente_stock_transacciones_y_movimientos_por_sucursal_coyoacan(): void
    {
        $admin = $this->crearUsuarioConRol('administrador');
        $escenario = $this->sembrarEscenarioMultisucursal();
        $coyoacan = $escenario['coyoacan'];
        $bellasArtes = $escenario['bellasArtes'];

        // Al seleccionar "Sucursal Coyoacán", el dashboard solo muestra datos y piezas de Coyoacán
        $response = $this->actingAs($admin)->get(route('admin.dashboard', [
            'sucursal_id' => $coyoacan->id,
        ]));

        $response->assertOk();
        $response->assertSee('SEDE: Sucursal Coyoacán');
        $response->assertSee('SKU-COYOACAN-1925');
        $response->assertSee('Manuscrito Exclusivo Coyoacán (1925)');
        // No debe mostrar los lotes exclusivos de Bellas Artes cuando Coyoacán está seleccionado
        $response->assertDontSee('SKU-BELLAS-1540');
        $response->assertDontSee('Incunable Exclusivo Bellas Artes (1540)');

        // El administrador sí puede cambiar explícitamente a Vista Global ("all") y ver ambas sedes
        $responseGlobal = $this->actingAs($admin)->get(route('admin.dashboard', [
            'sucursal_id' => 'all',
        ]));

        $responseGlobal->assertOk();
        $responseGlobal->assertSee('VISTA GLOBAL · TODAS LAS SEDES');
        $responseGlobal->assertSee('SKU-COYOACAN-1925');
        $responseGlobal->assertSee('SKU-BELLAS-1540');

        // Un empleado no administrador (ej. ventas) no accede a datos globales aunque envíe sucursal_id=all
        $vendedor = $this->crearUsuarioConRol('ventas');
        $responseVendedor = $this->actingAs($vendedor)
            ->withSession([SucursalContextoService::SESSION_SUCURSAL_KEY => $coyoacan->id])
            ->get(route('admin.dashboard', ['sucursal_id' => 'all']));

        $responseVendedor->assertOk();
        $responseVendedor->assertSee('SEDE: Sucursal Coyoacán');
        $responseVendedor->assertSee('SKU-COYOACAN-1925');
        $responseVendedor->assertDontSee('SKU-BELLAS-1540');
    }

    public function test_filtros_de_temporalidad_integrados_con_sucursal_seleccionada(): void
    {
        $admin = $this->crearUsuarioConRol('administrador');
        $escenario = $this->sembrarEscenarioMultisucursal();
        $coyoacan = $escenario['coyoacan'];
        $skuCoyoacan = $escenario['skuCoyoacan'];

        $existenciaCoyoacan = Existencia::where('sucursal_id', $coyoacan->id)->firstOrFail();

        // Movimiento registrado hoy en Coyoacán
        MovimientoInventario::create([
            'existencia_id' => $existenciaCoyoacan->id,
            'usuario_id' => $admin->id,
            'tipo' => 'entrada',
            'cantidad' => 2,
            'motivo' => 'Recepción Curatorial Hoy Coyoacán',
            'created_at' => now(),
        ]);

        // Movimiento registrado hace 20 días en Coyoacán (entra en 30D pero NO en HOY ni 7D)
        MovimientoInventario::create([
            'existencia_id' => $existenciaCoyoacan->id,
            'usuario_id' => $admin->id,
            'tipo' => 'ajuste',
            'cantidad' => 1,
            'motivo' => 'Auditoría Mensual Hace 20 Días Coyoacán',
            'created_at' => now()->subDays(20),
        ]);

        // 1. Filtro simultáneo: Sucursal Coyoacán + Período HOY
        $resHoy = $this->actingAs($admin)->get(route('admin.dashboard', [
            'sucursal_id' => $coyoacan->id,
            'periodo' => 'hoy',
        ]));

        $resHoy->assertOk();
        $resHoy->assertSee('SUCURSAL COYOACÁN (HOY)');
        $resHoy->assertSee('Recepción Curatorial Hoy Coyoacán');
        $resHoy->assertDontSee('Auditoría Mensual Hace 20 Días Coyoacán');

        // 2. Filtro simultáneo: Sucursal Coyoacán + Período 30D
        $res30d = $this->actingAs($admin)->get(route('admin.dashboard', [
            'sucursal_id' => $coyoacan->id,
            'periodo' => '30d',
        ]));

        $res30d->assertOk();
        $res30d->assertSee('SUCURSAL COYOACÁN (30D)');
        $res30d->assertSee('Recepción Curatorial Hoy Coyoacán');
        $res30d->assertSee('Auditoría Mensual Hace 20 Días Coyoacán');
    }

    public function test_rol_almacen_puede_navegar_y_operar_todas_las_secciones_del_menu_lateral(): void
    {
        $almacenista = $this->crearUsuarioConRol('almacen');
        $escenario = $this->sembrarEscenarioMultisucursal();
        $bellasArtes = $escenario['bellasArtes'];
        $coyoacan = $escenario['coyoacan'];
        $skuBellasArtes = $escenario['skuBellasArtes'];

        // 1. Panel Operativo de Sede (/admin) y (/almacen)
        $resDashboard = $this->actingAs($almacenista)->get(route('admin.dashboard'));
        $resDashboard->assertOk();
        $resDashboard->assertSee('Panel Operativo de Sede');
        $resDashboard->assertSee(route('admin.reportes'), false);
        $resDashboard->assertSee(route('admin.inventario'), false);
        $resDashboard->assertSee(route('almacen.ajustes'), false);
        $resDashboard->assertSee(route('almacen.transferencias'), false);
        $resDashboard->assertSee(route('almacen.envios'), false);

        $this->actingAs($almacenista)->get(route('almacen.dashboard'))->assertOk();

        // 2. Reportes & Exportación (/admin/reportes)
        $resReportes = $this->actingAs($almacenista)->get(route('admin.reportes'));
        $resReportes->assertOk();
        $resReportes->assertSee('Reportes & Exportación');

        // 3. Inventario Unificado (/admin/inventario)
        $resInventario = $this->actingAs($almacenista)->get(route('admin.inventario'));
        $resInventario->assertOk();

        // 4. Ajustes & Recepciones (/almacen/ajustes y /almacen/recepciones)
        $resAjustes = $this->actingAs($almacenista)->get(route('almacen.ajustes'));
        $resAjustes->assertOk();
        $resAjustes->assertSee('Ajustes de Stock & Recepciones');

        $resRecepciones = $this->actingAs($almacenista)->get(route('almacen.recepciones'));
        $resRecepciones->assertOk();

        $existenciaBellasArtes = Existencia::where('sucursal_id', $bellasArtes->id)
            ->where('variante_sku_id', $skuBellasArtes->id)
            ->firstOrFail();

        $resPostAjuste = $this->actingAs($almacenista)->post(route('almacen.ajustes.store'), [
            'existencia_id' => $existenciaBellasArtes->id,
            'tipo' => 'entrada',
            'cantidad' => 2,
            'motivo' => 'Recepción certificada en bóveda central',
        ]);
        $resPostAjuste->assertRedirect();
        $this->assertSame(7, (int) $existenciaBellasArtes->fresh()->cantidad);

        // 5. Transferencias de Stock (/almacen/transferencias)
        $resTransferencias = $this->actingAs($almacenista)->get(route('almacen.transferencias'));
        $resTransferencias->assertOk();
        $resTransferencias->assertSee('Transferencias de Stock Inter-Sucursal');

        $resPostTransferencia = $this->actingAs($almacenista)->post(route('almacen.transferencias.store'), [
            'sucursal_origen_id' => $bellasArtes->id,
            'sucursal_destino_id' => $coyoacan->id,
            'variante_sku_id' => $skuBellasArtes->id,
            'cantidad' => 1,
        ]);
        $resPostTransferencia->assertRedirect();
        $this->assertSame(6, (int) $existenciaBellasArtes->fresh()->cantidad);

        // 6. Envíos & Despacho (/almacen/envios)
        $resEnvios = $this->actingAs($almacenista)->get(route('almacen.envios'));
        $resEnvios->assertOk();
        $resEnvios->assertSee('Envíos y Despacho de Pedidos');
    }
}
