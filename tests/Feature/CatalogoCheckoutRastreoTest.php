<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Usuario;
use Database\Seeders\CatalogoProductoSeeder;
use Database\Seeders\UsuarioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogoCheckoutRastreoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            UsuarioSeeder::class,
            CatalogoProductoSeeder::class,
        ]);
    }

    public function test_seeder_crea_productos_por_categoria_con_sku_y_stock(): void
    {
        $this->assertGreaterThanOrEqual(
            4,
            Producto::whereHas('catalogo', fn ($q) => $q->where('tipo', 'libro'))->count()
        );
        $this->assertGreaterThanOrEqual(
            4,
            Producto::whereHas('catalogo', fn ($q) => $q->where('tipo', 'disco'))->count()
        );
        $this->assertGreaterThanOrEqual(
            4,
            Producto::whereHas('catalogo', fn ($q) => $q->where('tipo', 'coleccionable'))->count()
        );

        $libro = Producto::with(['variantesSku.existencias'])
            ->whereHas('catalogo', fn ($q) => $q->where('tipo', 'libro'))
            ->first();

        $this->assertNotNull($libro);
        $this->assertNotEmpty($libro->variantesSku);
        $this->assertGreaterThan(0, $libro->variantesSku->first()->existencias->sum('cantidad'));
    }

    public function test_rutas_de_categorias_filtran_exclusivamente_sus_productos(): void
    {
        $responseLibros = $this->get(route('public.libros'));
        $responseLibros->assertStatus(200);
        $responseLibros->assertSee('Cien Años de Soledad');
        $responseLibros->assertDontSee('Kind of Blue (Mono 1st Press 6-Eye)');
        $responseLibros->assertDontSee('Carta Manuscrita Bellas Artes (1928)');

        $responseVinilos = $this->get(route('public.vinilos'));
        $responseVinilos->assertStatus(200);
        $responseVinilos->assertSee('Kind of Blue (Mono 1st Press 6-Eye)');
        $responseVinilos->assertDontSee('Cien Años de Soledad');
        $responseVinilos->assertDontSee('Carta Manuscrita Bellas Artes (1928)');

        $responseMemorabilia = $this->get(route('public.memorabilia'));
        $responseMemorabilia->assertStatus(200);
        $responseMemorabilia->assertSee('Carta Manuscrita Bellas Artes (1928)');
        $responseMemorabilia->assertDontSee('Cien Años de Soledad');
        $responseMemorabilia->assertDontSee('Kind of Blue (Mono 1st Press 6-Eye)');

        $responseAdquisiciones = $this->get(route('public.adquisiciones'));
        $responseAdquisiciones->assertStatus(200);
        $responseAdquisiciones->assertSee('Adquisiciones Selectas');
    }

    public function test_flujo_completo_de_carrito_y_checkout_simulado_con_cualquier_tarjeta(): void
    {
        $cliente = Usuario::whereHas('roles', fn ($q) => $q->where('nombre', 'cliente'))->firstOrFail();
        $producto = Producto::with('variantesSku')
            ->whereHas('catalogo', fn ($q) => $q->where('tipo', 'libro'))
            ->firstOrFail();
        $variante = $producto->variantesSku->firstOrFail();

        $this->actingAs($cliente)
            ->post(route('carrito.agregar'), [
                'variante_sku_id' => $variante->id,
                'cantidad' => 1,
            ])
            ->assertRedirect();

        $this->actingAs($cliente)
            ->get(route('checkout.index'))
            ->assertStatus(200)
            ->assertSee($producto->nombre);

        $responsePago = $this->actingAs($cliente)
            ->post(route('checkout.pagar'), [
                'nombre_completo' => 'Elena Poniatowska Amor',
                'email' => $cliente->email,
                'telefono' => '5551234567',
                'calle' => 'Av. Juárez 14',
                'colonia' => 'Centro Histórico',
                'ciudad' => 'Ciudad de México',
                'estado' => 'CDMX',
                'codigo_postal' => '06010',
                'nombre_titular' => 'Elena Poniatowska',
                'numero_tarjeta' => '9999 0000 1111 2222',
                'expiracion' => '12/29',
                'cvv' => '777',
            ]);

        $pedido = Pedido::with('envios')->latest('id')->first();
        $this->assertNotNull($pedido);
        $this->assertSame('pagado', $pedido->estatus);

        $responsePago->assertRedirect(route('checkout.confirmacion', $pedido->id));

        $this->assertDatabaseHas('pagos', [
            'pedido_id' => $pedido->id,
            'metodo_pago' => 'tarjeta',
            'estatus' => 'aprobado',
        ]);

        $this->assertDatabaseHas('envios', [
            'pedido_id' => $pedido->id,
            'estado_actual' => 'en_preparacion',
        ]);

        $envio = $pedido->envios->firstOrFail();

        $this->actingAs($cliente)
            ->get(route('checkout.confirmacion', $pedido->id))
            ->assertStatus(200)
            ->assertSee('Bóveda Preparando Lote')
            ->assertSee($envio->numero_guia);
    }

    public function test_simulacion_interactiva_de_seguimiento_de_paquete(): void
    {
        $cliente = Usuario::whereHas('roles', fn ($q) => $q->where('nombre', 'cliente'))->firstOrFail();
        $variante = Producto::with('variantesSku')->firstOrFail()->variantesSku->firstOrFail();

        $this->actingAs($cliente)->post(route('carrito.agregar'), [
            'variante_sku_id' => $variante->id,
            'cantidad' => 1,
        ]);

        $this->actingAs($cliente)->post(route('checkout.pagar'), [
            'nombre_completo' => 'Cliente Archivo',
            'email' => $cliente->email,
            'telefono' => '5559876543',
            'calle' => 'Av. Universidad 3000',
            'colonia' => 'Coyoacán',
            'ciudad' => 'Ciudad de México',
            'estado' => 'CDMX',
            'codigo_postal' => '04510',
            'nombre_titular' => 'Cliente Archivo',
            'numero_tarjeta' => '12345678',
            'expiracion' => '08/28',
            'cvv' => '123',
        ]);

        $pedido = Pedido::with('envios')->latest('id')->firstOrFail();
        $this->assertSame('en_preparacion', $pedido->envios->firstOrFail()->estado_actual);

        // Avanzar al paso 2: Empaque Notariado (en_transito)
        $this->actingAs($cliente)
            ->post(route('checkout.simular-envio', $pedido->id))
            ->assertRedirect();

        $pedido->refresh();
        $this->assertSame('en_transito', $pedido->envios->firstOrFail()->estado_actual);

        // Avanzar al paso 3: En Tránsito con Custodia (en_camino)
        $this->actingAs($cliente)
            ->post(route('checkout.simular-envio', $pedido->id))
            ->assertRedirect();

        $pedido->refresh();
        $this->assertSame('en_camino', $pedido->envios->firstOrFail()->estado_actual);

        // Avanzar al paso 4: Entregado en Sede / Domicilio (entregado)
        $this->actingAs($cliente)
            ->post(route('checkout.simular-envio', $pedido->id))
            ->assertRedirect();

        $pedido->refresh();
        $envio = $pedido->envios->firstOrFail();
        $this->assertSame('entregado', $envio->estado_actual);
        $this->assertSame('entregado', $pedido->estatus);

        // Verificar que se muestre en el panel del cliente (/mis-pedidos)
        $this->actingAs($cliente)
            ->get(route('cliente.mis-pedidos'))
            ->assertStatus(200)
            ->assertSee('Entregado en Sede / Domicilio')
            ->assertSee($envio->numero_guia);
    }
}
