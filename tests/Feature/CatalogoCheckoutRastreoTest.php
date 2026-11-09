<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Controllers\CheckoutSimuladoController;
use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\PiezaUnica;
use App\Models\Producto;
use App\Models\Sucursal;
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

    public function test_pieza_unica_bloquea_concurrencia_entre_dos_clientes_y_libera_tras_5_minutos_o_consolida_al_pagar(): void
    {
        $clienteA = Usuario::whereHas('roles', fn ($q) => $q->where('nombre', 'cliente'))->firstOrFail();

        // 1. Verificar semillas diferenciadas de Piezas Únicas 1/1 por sucursal (Inventario Híbrido)
        $piezaBellasArtes = PiezaUnica::with('existencia.sucursal', 'existencia.varianteSku')
            ->where('numero_serie', 'ARC-LIB-1949-003')
            ->firstOrFail();
        $this->assertSame('Sucursal Central Bellas Artes', $piezaBellasArtes->existencia->sucursal->nombre);
        $this->assertSame('LIB-1949-LOS', $piezaBellasArtes->existencia->varianteSku->sku);
        $this->assertSame('disponible', $piezaBellasArtes->estado);

        $piezaCoyoacan = PiezaUnica::with('existencia.sucursal', 'existencia.varianteSku')
            ->where('numero_serie', 'ARC-LIB-1955-002')
            ->firstOrFail();
        $this->assertSame('Sucursal Coyoacán', $piezaCoyoacan->existencia->sucursal->nombre);
        $this->assertSame('LIB-1955-FCE', $piezaCoyoacan->existencia->varianteSku->sku);
        $this->assertSame('disponible', $piezaCoyoacan->estado);

        $piezaAlmacen = PiezaUnica::with('existencia.sucursal', 'existencia.varianteSku')
            ->where('numero_serie', 'ARC-LIB-1967-001')
            ->firstOrFail();
        $this->assertSame('Almacén General', $piezaAlmacen->existencia->sucursal->nombre);
        $this->assertSame('LIB-1967-SUD', $piezaAlmacen->existencia->varianteSku->sku);
        $this->assertSame('disponible', $piezaAlmacen->estado);

        $varianteUnica = $piezaBellasArtes->existencia->varianteSku;
        $this->assertSame(1, (int) $varianteUnica->existencias()->sum('cantidad'));

        // Verificar que la tarjeta pública muestra el borde morado intenso (border-purple-600), el distintivo Pieza Única · Edición Coleccionable y su número de serie
        $this->get(route('public.libros'))
            ->assertStatus(200)
            ->assertSee('border-purple-600', false)
            ->assertSee('Pieza Única · Edición Coleccionable')
            ->assertSee('PIEZA ÚNICA 1/1')
            ->assertSee('ARC-LIB-1949-003');

        // 2. Cliente A añade la Pieza Única al carrito -> se bloquea inmediatamente por 5 minutos
        $this->actingAs($clienteA)
            ->from(route('public.libros'))
            ->post(route('carrito.agregar'), [
                'variante_sku_id' => $varianteUnica->id,
                'cantidad' => 1,
            ])
            ->assertRedirect(route('public.libros'))
            ->assertSessionHasNoErrors();

        $piezaBellasArtes->refresh();
        $this->assertSame('reservada', $piezaBellasArtes->estado);
        $this->assertSame(0, (int) $varianteUnica->existencias()->sum('cantidad'));

        // El catálogo público ahora muestra la pieza como RESERVADA EN BÓVEDA (5 MIN)
        $this->get(route('public.libros'))
            ->assertStatus(200)
            ->assertSee('RESERVADA EN BÓVEDA (5 MIN)');

        // 3. Concurrencia: Otro cliente intenta adquirir la misma Pieza Única mientras está bloqueada -> 422 / Error
        $this->postJson(route('carrito.agregar'), [
            'variante_sku_id' => $varianteUnica->id,
            'cantidad' => 1,
        ])
            ->assertStatus(422)
            ->assertJsonFragment(['status' => 'error']);

        // 4. Expiración del temporizador de 5 minutos sin pago -> libera bloqueo automáticamente
        $this->travel(6)->minutes();

        $this->get(route('public.libros'))
            ->assertStatus(200);

        $piezaBellasArtes->refresh();
        $this->assertSame('disponible', $piezaBellasArtes->estado);
        $this->assertSame(1, (int) $varianteUnica->existencias()->sum('cantidad'));

        // 5. Ahora otro cliente puede reservar la Pieza Única y consolidar la compra pagando dentro de los 5 minutos
        $this->actingAs($clienteA)
            ->post(route('carrito.agregar'), [
                'variante_sku_id' => $varianteUnica->id,
                'cantidad' => 1,
            ])
            ->assertRedirect();

        $piezaBellasArtes->refresh();
        $this->assertSame('reservada', $piezaBellasArtes->estado);

        $this->actingAs($clienteA)
            ->post(route('checkout.pagar'), [
                'nombre_completo' => 'Coleccionista Archivum',
                'email' => $clienteA->email,
                'telefono' => '5551112233',
                'calle' => 'Av. Juárez 1',
                'colonia' => 'Centro Histórico',
                'ciudad' => 'Ciudad de México',
                'estado' => 'CDMX',
                'codigo_postal' => '06050',
                'nombre_titular' => 'Coleccionista Archivum',
                'numero_tarjeta' => '4111222233334444',
                'expiracion' => '12/29',
                'cvv' => '999',
            ])
            ->assertRedirect();

        $piezaBellasArtes->refresh();
        $this->assertSame('vendida', $piezaBellasArtes->estado);
        $this->assertSame(0, (int) $varianteUnica->existencias()->sum('cantidad'));
    }

    public function test_inventario_hibrido_permite_compra_multiple_en_stock_masivo_y_filtra_catalogo_diferenciado_por_sucursal(): void
    {
        $cliente = Usuario::whereHas('roles', fn ($q) => $q->where('nombre', 'cliente'))->firstOrFail();

        $bellasArtes = Sucursal::where('nombre', 'like', '%Bellas Artes%')->firstOrFail();
        $coyoacan = Sucursal::where('nombre', 'like', '%Coyoacán%')->firstOrFail();
        $almacen = Sucursal::where('nombre', 'like', '%Almacén General%')->firstOrFail();

        // 1. Variabilidad por sucursal: cada sede tiene libros, discos y coleccionables propios sin repetir inventario
        $resBellasArtes = $this->get(route('public.libros', ['sucursal_id' => $bellasArtes->id]));
        $resBellasArtes->assertOk()
            ->assertSee('El Aleph (Ejemplar Dedicado por el Autor)')
            ->assertSee('El Laberinto de la Soledad')
            ->assertDontSee('Pedro Páramo (Edición Príncipe FCE)')
            ->assertDontSee('Aura (Edición Conmemorativa Biblioteca Era Coyoacán)')
            ->assertDontSee('Cien Años de Soledad')
            ->assertDontSee('Rayuela (Tiraje Clásico Editorial Sudamericana)');

        $resCoyoacan = $this->get(route('public.libros', ['sucursal_id' => $coyoacan->id]));
        $resCoyoacan->assertOk()
            ->assertSee('Pedro Páramo (Edición Príncipe FCE)')
            ->assertSee('Aura (Edición Conmemorativa Biblioteca Era Coyoacán)')
            ->assertDontSee('El Aleph (Ejemplar Dedicado por el Autor)')
            ->assertDontSee('El Laberinto de la Soledad')
            ->assertDontSee('Cien Años de Soledad');

        $resAlmacen = $this->get(route('public.libros', ['sucursal_id' => $almacen->id]));
        $resAlmacen->assertOk()
            ->assertSee('Cien Años de Soledad')
            ->assertSee('Rayuela (Tiraje Clásico Editorial Sudamericana)')
            ->assertDontSee('El Aleph (Ejemplar Dedicado por el Autor)')
            ->assertDontSee('Pedro Páramo (Edición Príncipe FCE)');

        // 2. Stock Masivo (> 1): permite compra de varias unidades sin bloquear de forma exclusiva el producto
        $productoMasivo = Producto::with('variantesSku.existencias')
            ->where('nombre', 'Aura (Edición Conmemorativa Biblioteca Era Coyoacán)')
            ->firstOrFail();
        $varianteMasiva = $productoMasivo->variantesSku->firstOrFail();

        $this->assertSame(9, (int) $varianteMasiva->existencias()->sum('cantidad'));
        $this->assertNull(CheckoutSimuladoController::obtenerPiezaUnicaDeVariante($varianteMasiva->id));

        // Cliente A reserva 3 unidades de golpe
        $this->actingAs($cliente)
            ->from(route('public.libros', ['sucursal_id' => $coyoacan->id]))
            ->post(route('carrito.agregar'), [
                'variante_sku_id' => $varianteMasiva->id,
                'cantidad' => 3,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        // Quedan 6 unidades libres en bóveda y el producto NO se bloquea de forma exclusiva
        $this->assertSame(6, (int) $varianteMasiva->existencias()->sum('cantidad'));

        // Otro cliente puede adquirir simultáneamente 2 unidades adicionales vía JSON sin bloqueo de concurrencia
        $this->postJson(route('carrito.agregar'), [
            'variante_sku_id' => $varianteMasiva->id,
            'cantidad' => 2,
        ])
            ->assertOk()
            ->assertJsonFragment(['status' => 'success']);

        $this->assertSame(4, (int) $varianteMasiva->existencias()->sum('cantidad'));
    }

    public function test_promociones_exclusivas_preventa_clientes_frecuentes_5_compras_apertura_publica_y_countdown_animado(): void
    {
        $usuarioFrecuente = Usuario::where('email', 'elena.ramirez@gmail.com')->firstOrFail();
        $usuarioRegular = Usuario::where('email', 'javier.morales@hotmail.com')->firstOrFail();

        $clienteFrecuente = Cliente::where('usuario_id', $usuarioFrecuente->id)->firstOrFail();
        $clienteRegular = Cliente::where('usuario_id', $usuarioRegular->id)->firstOrFail();

        // 1. Verificar regla de Cliente Frecuente (>= 5 compras) vs Cliente Común (< 5 compras)
        $this->assertGreaterThanOrEqual(5, $clienteFrecuente->totalComprasRealizadas());
        $this->assertTrue($clienteFrecuente->esClienteFrecuente());

        $this->assertLessThan(5, $clienteRegular->totalComprasRealizadas());
        $this->assertFalse($clienteRegular->esClienteFrecuente());

        // 2. Verificar que la página principal y el panel del cliente renderizan las tarjetas animadas y el contador regresivo
        $this->actingAs($usuarioFrecuente)
            ->get(route('public.home'))
            ->assertOk()
            ->assertSee('promo-card-animated', false)
            ->assertSee('promo-shimmer-badge', false)
            ->assertSee('data-promo-countdown', false)
            ->assertSee('Preventa Activa')
            ->assertSee('Cliente Frecuente VIP');

        $this->actingAs($usuarioFrecuente)
            ->get(route('cliente.mis-pedidos'))
            ->assertOk()
            ->assertSee('Tienes Acceso Anticipado a las Preventas Exclusivas de Bóveda')
            ->assertSee('data-promo-countdown', false);

        // 3. Obtener pieza en Fase de Preventa Exclusiva VIP ("Programa Inaugural Palacio de Bellas Artes (1934)")
        $productoPreventaVip = Producto::with(['variantesSku', 'promociones'])
            ->where('nombre', 'Programa Inaugural Palacio de Bellas Artes (1934)')
            ->firstOrFail();
        $varianteVip = $productoPreventaVip->variantesSku->firstOrFail();
        $promoVip = CheckoutSimuladoController::obtenerPromocionDeProducto($productoPreventaVip);

        $this->assertNotNull($promoVip);
        $this->assertSame('preventa_exclusiva', $promoVip->faseActual());

        // 4. Cliente común (< 5 compras) intenta adquirir en Fase de Preventa Exclusiva -> Bloqueado (422)
        $this->actingAs($usuarioRegular)
            ->postJson(route('carrito.agregar'), [
                'variante_sku_id' => $varianteVip->id,
                'cantidad' => 1,
            ])
            ->assertStatus(422)
            ->assertJsonFragment(['status' => 'error']);

        // 5. Cliente Frecuente (>= 5 compras) aparta la pieza durante la Fase de Preventa Exclusiva -> Permitido
        $this->actingAs($usuarioFrecuente)
            ->postJson(route('carrito.agregar'), [
                'variante_sku_id' => $varianteVip->id,
                'cantidad' => 1,
            ])
            ->assertOk()
            ->assertJsonFragment(['status' => 'success']);

        // 6. Fase de Apertura (Público General): llega el día de la apertura oficial y se desbloquea para clientes comunes
        $this->travel(3)->days();

        $promoVip->refresh();
        $this->assertSame('abierta_publico', $promoVip->faseActual());

        $this->actingAs($usuarioRegular)
            ->postJson(route('carrito.agregar'), [
                'variante_sku_id' => $varianteVip->id,
                'cantidad' => 1,
            ])
            ->assertOk()
            ->assertJsonFragment(['status' => 'success']);

        // 7. Personal de Ventas puede programar una nueva promoción con preventa anticipada desde el panel
        $usuarioVentas = Usuario::where('email', 'ventas1@archivum.com')->firstOrFail();
        $this->actingAs($usuarioVentas)
            ->post(route('ventas.promociones.store'), [
                'nombre' => 'Lanzamiento Especial Invierno: Códice Coyoacán',
                'descripcion' => 'Preventa anticipada para clientes con 5+ compras.',
                'producto_id' => $productoPreventaVip->id,
                'tipo_descuento' => 'porcentaje',
                'valor' => 20,
                'es_preventa_exclusiva' => 1,
                'preventa_inicia_en' => now()->toDateString(),
                'apertura_publica_en' => now()->addDays(4)->toDateString(),
                'termina_en' => now()->addDays(12)->toDateString(),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('promociones', [
            'nombre' => 'Lanzamiento Especial Invierno: Códice Coyoacán',
            'tipo_descuento' => 'porcentaje',
        ]);
    }
}
