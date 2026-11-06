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

    public function test_tarjeta_de_producto_con_stock_cero_muestra_etiqueta_roja_agotado_y_deshabilita_boton_reservar(): void
    {
        $productoAgotado = Producto::with('variantesSku.existencias')
            ->where('nombre', 'Fotografía en Plata sobre Gelatina Firmada (1934)')
            ->firstOrFail();

        $this->assertSame(0, (int) $productoAgotado->variantesSku->firstOrFail()->existencias->sum('cantidad'));

        $response = $this->get(route('public.memorabilia'));
        $response->assertStatus(200);
        $response->assertSee('Fotografía en Plata sobre Gelatina Firmada (1934)');
        $response->assertSee('AGOTADO');
        $response->assertDontSee('0 en bóveda');
        $response->assertSee('bg-red-600', false);
        $response->assertSee('cursor-not-allowed', false);
    }

    public function test_backend_bloquea_agregar_al_carrito_o_comprar_articulo_con_stock_cero(): void
    {
        $cliente = Usuario::whereHas('roles', fn ($q) => $q->where('nombre', 'cliente'))->firstOrFail();
        $productoAgotado = Producto::with('variantesSku')
            ->where('nombre', 'Fotografía en Plata sobre Gelatina Firmada (1934)')
            ->firstOrFail();
        $varianteAgotada = $productoAgotado->variantesSku->firstOrFail();

        // 1. Intento de agregar al carrito vía petición manipulada (Web)
        $responseAgregar = $this->actingAs($cliente)
            ->from(route('public.memorabilia'))
            ->post(route('carrito.agregar'), [
                'variante_sku_id' => $varianteAgotada->id,
                'cantidad' => 1,
            ]);

        $responseAgregar->assertRedirect(route('public.memorabilia'));
        $responseAgregar->assertSessionHasErrors('stock');
        $this->assertStringContainsString(
            'no se encuentra disponible en bóveda',
            (string) session('error')
        );
        $this->assertEmpty(session('carrito_archivum', []));

        // 2. Intento de agregar al carrito vía petición JSON / AJAX
        $this->actingAs($cliente)
            ->postJson(route('carrito.agregar'), [
                'variante_sku_id' => $varianteAgotada->id,
                'cantidad' => 1,
            ])
            ->assertStatus(422)
            ->assertJsonFragment(['status' => 'error']);

        // 3. Intento de forzar el pago en checkout si el artículo se agotó mientras estaba en sesión
        $pedidosAntes = Pedido::count();

        $responseCheckoutForzado = $this->actingAs($cliente)
            ->withSession(['carrito_archivum' => [$varianteAgotada->id => 1]])
            ->from(route('checkout.index'))
            ->post(route('checkout.pagar'), [
                'nombre_completo' => 'Cliente Archivo',
                'email' => $cliente->email,
                'telefono' => '5559876543',
                'calle' => 'Av. Universidad 3000',
                'colonia' => 'Coyoacán',
                'ciudad' => 'Ciudad de México',
                'estado' => 'CDMX',
                'codigo_postal' => '04510',
                'nombre_titular' => 'Cliente Archivo',
                'numero_tarjeta' => '4111222233334444',
                'expiracion' => '08/28',
                'cvv' => '123',
            ]);

        $responseCheckoutForzado->assertRedirect(route('checkout.index'));
        $responseCheckoutForzado->assertSessionHasErrors('stock');
        $this->assertSame($pedidosAntes, Pedido::count());
    }

    public function test_reserva_temporal_disminuye_stock_inmediatamente_y_libera_al_expirar_5_minutos(): void
    {
        $cliente = Usuario::whereHas('roles', fn ($q) => $q->where('nombre', 'cliente'))->firstOrFail();

        // "Partitura Autógrafa de Estudio en Sol Menor (1912)" tiene 2 unidades en bóveda inicialmente
        $producto = Producto::with('variantesSku.existencias')
            ->where('nombre', 'Partitura Autógrafa de Estudio en Sol Menor (1912)')
            ->firstOrFail();
        $variante = $producto->variantesSku->firstOrFail();

        $this->assertSame(2, (int) $variante->existencias()->sum('cantidad'));

        // 1. El cliente pulsa "RESERVAR" (añade 1 unidad al carrito sin haber pagado aún)
        $this->actingAs($cliente)
            ->from(route('public.memorabilia'))
            ->post(route('carrito.agregar'), [
                'variante_sku_id' => $variante->id,
                'cantidad' => 1,
            ])
            ->assertRedirect(route('public.memorabilia'));

        // El stock en base de datos pasa inmediatamente de 2 a 1
        $this->assertSame(1, (int) $variante->existencias()->sum('cantidad'));

        // Existe una reserva activa con expiración a 5 minutos
        $this->assertDatabaseHas('reservas_stock', [
            'cantidad' => 1,
            'estado' => 'activa',
        ]);

        // En el catálogo público se muestra "1 en bóveda" y el temporizador de 5 minutos en el drawer
        $responseCatalogo = $this->actingAs($cliente)->get(route('public.memorabilia'));
        $responseCatalogo->assertStatus(200);
        $responseCatalogo->assertSee('1 en bóveda');
        $responseCatalogo->assertSee('Reserva Temporal (5 min):');

        // 2. Avanzamos el reloj 6 minutos sin concretar la compra (expira el countdown de 5 minutos)
        $this->travel(6)->minutes();

        // Al consultar el catálogo, el sistema libera automáticamente la reserva vencida y devuelve el stock a 2
        $responseTrasExpirar = $this->actingAs($cliente)->get(route('public.memorabilia'));
        $responseTrasExpirar->assertStatus(200);

        $this->assertSame(2, (int) $variante->existencias()->sum('cantidad'));
        $this->assertDatabaseHas('reservas_stock', [
            'cantidad' => 1,
            'estado' => 'expirada',
        ]);
        $this->assertEmpty(session('carrito_archivum', []));
    }

    public function test_control_estricto_en_preventas_bloquea_sobreventa_sobre_lote_autorizado(): void
    {
        $cliente = Usuario::whereHas('roles', fn ($q) => $q->where('nombre', 'cliente'))->firstOrFail();

        $productoPreventa = Producto::with('variantesSku.existencias')
            ->where('nombre', 'El Laberinto de la Soledad (1ª Edición Cuadernos Americanos)')
            ->firstOrFail();

        // Configurar pieza como preventa con lote autorizado por compras de máximo 2 unidades
        $productoPreventa->update([
            'es_preventa' => true,
            'limite_preventa' => 2,
        ]);

        $variante = $productoPreventa->variantesSku->firstOrFail();

        // Reservar 2 unidades (dentro del lote autorizado por compras) es permitido
        $this->actingAs($cliente)
            ->from(route('public.libros'))
            ->post(route('carrito.agregar'), [
                'variante_sku_id' => $variante->id,
                'cantidad' => 2,
            ])
            ->assertRedirect(route('public.libros'))
            ->assertSessionHasNoErrors();

        // Intentar reservar 1 unidad adicional (superando el lote autorizado de 2) se bloquea por sobreventa
        $responseSobreventa = $this->actingAs($cliente)
            ->from(route('public.libros'))
            ->post(route('carrito.agregar'), [
                'variante_sku_id' => $variante->id,
                'cantidad' => 1,
            ]);

        $responseSobreventa->assertRedirect(route('public.libros'));
        $responseSobreventa->assertSessionHasErrors('stock');
        $this->assertStringContainsString('Sobreventa bloqueada', (string) session('error'));
    }

    public function test_pagina_principal_renderiza_carrusel_3d_swiper_con_fichas_curatoriales(): void
    {
        $response = $this->get(route('public.home'));

        $response->assertStatus(200);
        $response->assertSee('swiper product3DCarousel', false);
        $response->assertSee('swiper-wrapper', false);
        $response->assertSee('swiper-slide', false);
        $response->assertSee("new Swiper('.product3DCarousel'", false);
        $response->assertSee("effect: 'coverflow'", false);
        $response->assertSee('Cien Años de Soledad');
        $response->assertSee('Kind of Blue');
        $response->assertSee('Carta Manuscrita Bellas Artes');
    }

    public function test_vista_vinilos_renderiza_showcase_3d_interactivo_con_controles_y_reserva(): void
    {
        $response = $this->get(route('public.vinilos'));

        $response->assertStatus(200);
        $response->assertSee('disc-showcase-stage', false);
        $response->assertSee('vinyl-surface', false);
        $response->assertSee('cd-surface', false);
        $response->assertSee('disc-spin-rotor', false);
        $response->assertSee('disc-flip-btn', false);
        $response->assertSee('disc-play-pause-btn', false);
        $response->assertSee('disc-replay-btn', false);
        $response->assertSee('showcase-reserve-form', false);
        $response->assertSee('Kind of Blue (Mono 1st Press 6-Eye)');
        $response->assertSee('The Dark Side of the Moon (1st Japan Black Triangle CD)');
    }
}
