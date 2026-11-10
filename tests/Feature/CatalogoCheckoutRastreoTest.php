<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Controllers\CheckoutSimuladoController;
use App\Models\Cliente;
use App\Models\Existencia;
use App\Models\Pedido;
use App\Models\PiezaUnica;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\Usuario;
use App\Models\VarianteSku;
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

        // Verificación de que el selector de sucursales no se muestra en la vista pública
        $responseHome = $this->get(route('public.home'));
        $responseHome->assertStatus(200)
            ->assertDontSee('id="public-branch-select"', false)
            ->assertDontSee('name="sucursal_id"', false)
            ->assertSee('Libros')
            ->assertSee('Discos')
            ->assertSee('Coleccionables');
    }

    public function test_filtros_interactivos_por_categoria_libros_discos_y_coleccionables(): void
    {
        // 1. Libros: Filtro interactivo por Géneros Literarios (novela, ensayo, poesía, historia, ciencia)
        $resNovelas = $this->get(route('public.libros', ['genero' => 'novela']));
        $resNovelas->assertStatus(200)
            ->assertSee('Cien Años de Soledad')
            ->assertSee('Pedro Páramo')
            ->assertDontSee('El Laberinto de la Soledad');

        $resEnsayos = $this->get(route('public.libros', ['genero' => 'ensayo']));
        $resEnsayos->assertStatus(200)
            ->assertSee('El Laberinto de la Soledad')
            ->assertDontSee('Cien Años de Soledad');

        $resPoesia = $this->get(route('public.libros', ['genero' => 'poesia']));
        $resPoesia->assertStatus(200)
            ->assertSee('Piedra de Sol')
            ->assertDontSee('Cien Años de Soledad');

        $resHistoria = $this->get(route('public.libros', ['genero' => 'historia']));
        $resHistoria->assertStatus(200)
            ->assertSee('Visión de los Vencidos')
            ->assertDontSee('Cien Años de Soledad');

        $resCiencia = $this->get(route('public.libros', ['genero' => 'ciencia']));
        $resCiencia->assertStatus(200)
            ->assertSee('La Estructura de las Revoluciones Científicas')
            ->assertDontSee('Cien Años de Soledad');

        // 2. Discos: Filtro interactivo para separar entre Vinilos y CDs
        $resVinilos = $this->get(route('public.vinilos', ['formato' => 'vinilo']));
        $resVinilos->assertStatus(200)
            ->assertSee('Kind of Blue (Mono 1st Press 6-Eye)')
            ->assertDontSee('The Dark Side of the Moon (1st Japan Black Triangle CD)');

        $resCds = $this->get(route('public.vinilos', ['formato' => 'cd']));
        $resCds->assertStatus(200)
            ->assertSee('The Dark Side of the Moon (1st Japan Black Triangle CD)')
            ->assertDontSee('Kind of Blue (Mono 1st Press 6-Eye)');

        // 3. Coleccionables: Filtro por categorías detalladas
        $resFiguras = $this->get(route('public.memorabilia', ['categoria' => 'figuras']));
        $resFiguras->assertStatus(200)
            ->assertSee('Estatua Evangelion')
            ->assertDontSee('Carta Manuscrita Bellas Artes (1928)');

        $resJuguetes = $this->get(route('public.memorabilia', ['categoria' => 'juguetes']));
        $resJuguetes->assertStatus(200)
            ->assertSee('Robot Mecánico de Hojalata')
            ->assertDontSee('Carta Manuscrita Bellas Artes (1928)');

        $resNumismatica = $this->get(route('public.memorabilia', ['categoria' => 'numismatica']));
        $resNumismatica->assertStatus(200)
            ->assertSee('Medalla Conmemorativa del Centenario (1910)')
            ->assertSee('Billete Revolucionario 10 Pesos')
            ->assertDontSee('Carta Manuscrita Bellas Artes (1928)');

        $resHistoricos = $this->get(route('public.memorabilia', ['categoria' => 'historicos']));
        $resHistoricos->assertStatus(200)
            ->assertSee('Carta Manuscrita Bellas Artes (1928)')
            ->assertDontSee('Estatua Evangelion');

        $resVideojuegos = $this->get(route('public.memorabilia', ['categoria' => 'videojuegos']));
        $resVideojuegos->assertStatus(200)
            ->assertSee('Consola Nintendo Game &amp; Watch', false)
            ->assertSee('Cartucho The Legend of Zelda')
            ->assertDontSee('Carta Manuscrita Bellas Artes (1928)');
    }

    public function test_checkout_vacio_y_cajon_de_boveda_tienen_centrado_vertical_en_pantalla(): void
    {
        // 1. Vista de Checkout vacía tiene contenedor con min-h-[70vh] y centrado vertical flexbox
        $responseCheckout = $this->get(route('checkout.index'));
        $responseCheckout->assertStatus(200)
            ->assertSee('min-h-[70vh]', false)
            ->assertSee('flex flex-col justify-center items-center', false)
            ->assertSee('Tu Bóveda de Adquisición está vacía')
            ->assertSee('Volver al Catálogo General');

        // 2. El cajón de Bóveda (Cart Drawer) incluye clases de centrado vertical para su estado vacío
        $responseHome = $this->get(route('public.home'));
        $responseHome->assertStatus(200)
            ->assertSee('id="cart-drawer"', false)
            ->assertSee('Tu Bóveda de Adquisición está vacía')
            ->assertSee('flex flex-col justify-center items-center', false);
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

    public function test_pagina_principal_renderiza_vitrina_desplazamiento_horizontal_nativo_sin_flechas_ni_indicadores(): void
    {
        $response = $this->get(route('public.home'));

        $response->assertStatus(200);
        // Contenedor nativo con scroll horizontal y scroll-snap
        $response->assertSee('id="vitrina-scroll-horizontal"', false);
        $response->assertSee('vitrina-scroll-container', false);
        $response->assertSee('overflow-x-auto', false);
        $response->assertSee('scroll-snap-type: x mandatory', false);
        $response->assertSee('scroll-snap-align: start', false);

        // Eliminación completa de flechas laterales y burbujas de paginación
        $response->assertDontSee('swiper-button-prev', false);
        $response->assertDontSee('swiper-button-next', false);
        $response->assertDontSee('swiper-pagination', false);

        // Fichas curatoriales presentes
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

    public function test_landing_page_renderiza_seccion_conoce_mas_de_nosotros_con_resumen_institucional_y_lluvia_de_elementos_tematicos(): void
    {
        $response = $this->get(route('public.home'));

        // En la página de Inicio la pestaña superior está presente, pero el bloque institucional no se duplica abajo, las tarjetas repetidas de salas fueron removidas y el botón del carrito no dice "Checkout ("
        $response->assertOk()
            ->assertSee('id="nav-tab-nosotros"', false)
            ->assertSee(route('public.nosotros'), false)
            ->assertDontSee('id="conoce-mas-de-nosotros"', false)
            ->assertDontSee('Explorar Sala &rarr;', false)
            ->assertDontSee('Checkout (');

        // Al hacer clic en la pestaña superior "Conoce más de nosotros" se renderiza su vista dedicada con resumen y lluvia animada
        $responseNosotros = $this->get(route('public.nosotros'));

        $responseNosotros->assertOk()
            ->assertSee('id="nav-tab-nosotros"', false)
            ->assertSee('id="conoce-mas-de-nosotros"', false)
            ->assertSee('Conoce más de nosotros')
            ->assertSee('Arquitectura de Resguardo &amp; Comercio Híbrido', false)
            ->assertSee('Sucursal Central Bellas Artes')
            ->assertSee('Sucursal Coyoacán')
            ->assertSee('Almacén General')
            ->assertSee('archivum-rain-section', false)
            ->assertSee('archivum-rain-layer', false)
            ->assertSee('archivum-rain-particle', false)
            ->assertSee('data-rain-item="libro"', false)
            ->assertSee('data-rain-item="vinilo"', false)
            ->assertSee('data-rain-item="coleccionable"', false)
            ->assertSee('dark:text-white', false)
            ->assertSee('dark:text-slate-100', false)
            ->assertSee('dark:text-amber-50', false);
    }

    public function test_modo_oscuro_modales_cajones_y_overlays_usan_superficies_nocturnas_sin_tonos_grises(): void
    {
        $appCss = file_get_contents(resource_path('css/app.css'));

        // 1. Verificar que no exista el valor grisáceo erróneo #b9b9b9 en app.css
        $this->assertStringNotContainsString('#b9b9b9', $appCss);

        // 2. Comprobar que surface-container-lowest en .dark sea una superficie nocturna de bóveda (#140d20)
        $this->assertStringContainsString('--color-surface-container-lowest: #140d20;', $appCss);

        // 3. Comprobar que existan reglas de fondo y backdrop oscuro para drawers y overlays
        $this->assertStringContainsString('.dark #login-drawer', $appCss);
        $this->assertStringContainsString('.dark #cart-drawer', $appCss);
        $this->assertStringContainsString('.dark #login-drawer-overlay', $appCss);
        $this->assertStringContainsString('rgba(7, 4, 13, 0.88)', $appCss);

        // 4. Comprobar que en la vista pública se renderizan los cajones con clases nocturnas y backdrops de alto contraste
        $response = $this->get(route('public.home'));
        $response->assertOk()
            ->assertSee('id="login-drawer-overlay"', false)
            ->assertSee('bg-[#07040d]/85', false)
            ->assertSee('id="login-drawer"', false)
            ->assertSee('dark:bg-[#171024]', false)
            ->assertSee('id="cart-drawer"', false);
    }

    public function test_tarjetas_de_producto_limpias_con_modal_interactivo_curatorial(): void
    {
        $response = $this->get(route('public.home'));

        $response->assertOk()
            // 1. Tarjetas limpias: miniatura con disponibilidad, nombre, subtítulo, botón de detalles, precio y reserva
            ->assertSee('data-product-details', false)
            ->assertSee('en bóveda')
            ->assertSee('Precio Tasado:')
            ->assertSee('Reservar')
            ->assertSee('Ver detalles &rarr;', false)
            // 2. Modal global curatorial presente para animación fade-in & zoom-in
            ->assertSee('id="product-curatorial-modal"', false)
            ->assertSee('id="product-modal-backdrop"', false)
            ->assertSee('id="product-modal-dialog"', false)
            ->assertSee('abrirModalDetalleProducto', false)
            ->assertSee('cerrarModalDetalleProducto', false);
    }

    public function test_cajon_de_boveda_renderiza_selector_de_cantidades_y_disponibilidad(): void
    {
        $variante = VarianteSku::whereHas('producto.catalogo', fn ($q) => $q->where('tipo', 'libro'))
            ->whereDoesntHave('existencias.piezasUnicas')
            ->firstOrFail();

        $this->post(route('carrito.agregar'), [
            'variante_sku_id' => $variante->id,
            'cantidad' => 1,
        ]);

        $response = $this->get(route('public.home'));
        $response->assertOk()
            ->assertSee('class="cart-qty-select', false)
            ->assertSee('data-update-url', false)
            ->assertSee('Cantidad:')
            ->assertSee('id="cart-drawer-subtotal"', false)
            ->assertSee('id="cart-drawer-total"', false);
    }

    public function test_carrito_permite_actualizar_cantidad_dinamicamente_con_recalculo_de_subtotal_y_stock(): void
    {
        $variante = VarianteSku::with('producto')
            ->whereHas('producto.catalogo', fn ($q) => $q->where('tipo', 'libro'))
            ->whereDoesntHave('existencias.piezasUnicas')
            ->firstOrFail();
        $precioUnitario = (float) ($variante->precio ?? $variante->producto->precio);

        // 1. Agregar inicialmente 1 unidad
        $this->post(route('carrito.agregar'), [
            'variante_sku_id' => $variante->id,
            'cantidad' => 1,
        ]);

        $stockInicial = (int) Existencia::where('variante_sku_id', $variante->id)->sum('cantidad');

        // 2. Actualizar vía PATCH a 2 unidades
        $responsePatch = $this->patchJson(route('carrito.actualizar', $variante->id), [
            'cantidad' => 2,
        ]);

        $responsePatch->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('carrito.cantidad_total', 2);

        $this->assertEquals($precioUnitario * 2, (float) $responsePatch->json('carrito.subtotal'));
        $this->assertEquals($precioUnitario * 2, (float) $responsePatch->json('carrito.total'));

        // Verificar que el stock en bóveda disminuyó en 1 unidad adicional
        $this->assertSame($stockInicial - 1, (int) Existencia::where('variante_sku_id', $variante->id)->sum('cantidad'));

        // 3. Reducir vía PATCH a 1 unidad
        $responseReduce = $this->patchJson(route('carrito.actualizar', $variante->id), [
            'cantidad' => 1,
        ]);

        $responseReduce->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('carrito.cantidad_total', 1);

        $this->assertEquals($precioUnitario, (float) $responseReduce->json('carrito.subtotal'));
        $this->assertEquals($precioUnitario, (float) $responseReduce->json('carrito.total'));

        // Verificar que el stock en bóveda se restauró
        $this->assertSame($stockInicial, (int) Existencia::where('variante_sku_id', $variante->id)->sum('cantidad'));
    }

    public function test_pieza_unica_bloquea_seleccion_de_multiples_unidades(): void
    {
        $piezaUnica = PiezaUnica::with('existencia.varianteSku.producto')->firstOrFail();
        $variante = $piezaUnica->existencia->varianteSku;

        $this->post(route('carrito.agregar'), [
            'variante_sku_id' => $variante->id,
            'cantidad' => 1,
        ]);

        $response = $this->patchJson(route('carrito.actualizar', $variante->id), [
            'cantidad' => 2,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('status', 'error');

        $this->assertStringContainsString('Pieza', (string) $response->json('message'));
        $this->assertStringContainsString('1/1', (string) $response->json('message'));
    }

    public function test_invitado_con_items_al_acceder_a_checkout_es_redirigido_a_autenticacion_protegiendo_lote(): void
    {
        $variante = VarianteSku::firstOrFail();

        // 1. Invitado agrega producto a la bóveda
        $this->post(route('carrito.agregar'), [
            'variante_sku_id' => $variante->id,
            'cantidad' => 1,
        ]);

        $this->assertNotEmpty(session('carrito_archivum'));

        // 2. Al intentar acceder a /checkout sin sesión, es interceptado y dirigido a autenticación
        $responseCheckout = $this->get(route('checkout.index'));
        $responseCheckout->assertRedirect(route('public.home', ['login' => '1', 'intended' => 'checkout']));
        $this->assertSame(route('checkout.index'), session('url.intended'));

        // 3. El carrito y el temporizador en sesión se mantienen intactos
        $this->assertNotEmpty(session('carrito_archivum'));
        $this->assertArrayHasKey($variante->id, session('carrito_archivum'));
        $this->assertNotEmpty(session('carrito_reservas_ids'));
    }

    public function test_login_redirige_a_checkout_y_conserva_carrito_tras_autenticacion(): void
    {
        $cliente = Usuario::whereHas('roles', fn ($q) => $q->where('nombre', 'cliente'))->firstOrFail();
        $variante = VarianteSku::firstOrFail();

        // 1. Invitado reserva un artículo
        $this->post(route('carrito.agregar'), [
            'variante_sku_id' => $variante->id,
            'cantidad' => 1,
        ]);

        // 2. Inicia sesión vía JSON con intended = checkout
        $responseLogin = $this->postJson(route('login'), [
            'email' => $cliente->email,
            'password' => 'Password123!',
            'intended' => route('checkout.index'),
        ]);

        $responseLogin->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('redirect', route('checkout.index'));

        $this->assertAuthenticatedAs($cliente);

        // 3. Accede inmediatamente a la pasarela notariada con sus piezas intactas
        $responseCheckout = $this->get(route('checkout.index'));
        $responseCheckout->assertOk()
            ->assertSee($variante->producto->nombre)
            ->assertSee('Formalización de Adquisición');
    }

    public function test_registro_redirige_a_checkout_y_conserva_carrito_tras_crear_cuenta(): void
    {
        $variante = VarianteSku::firstOrFail();

        // 1. Invitado reserva un artículo
        $this->post(route('carrito.agregar'), [
            'variante_sku_id' => $variante->id,
            'cantidad' => 1,
        ]);

        // 2. Se registra como nuevo cliente con intended = checkout
        $responseRegister = $this->postJson(route('register'), [
            'nombre' => 'Aura',
            'apellidos' => 'Fuentes Silva',
            'email' => 'aura.fuentes@archivum.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'intended' => route('checkout.index'),
        ]);

        $responseRegister->assertCreated()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('redirect', route('checkout.index'));

        $nuevoUsuario = Usuario::where('email', 'aura.fuentes@archivum.test')->firstOrFail();
        $this->assertAuthenticatedAs($nuevoUsuario);

        // 3. Accede a checkout con sus piezas reservadas
        $responseCheckout = $this->get(route('checkout.index'));
        $responseCheckout->assertOk()
            ->assertSee($variante->producto->nombre);
    }

    public function test_cajon_de_boveda_muestra_control_de_acceso_segun_sesion(): void
    {
        // 1. Como invitado, se renderiza el botón de validación de acceso
        $resGuest = $this->get(route('public.home'));
        $resGuest->assertOk()
            ->assertSee('id="btn-proceder-pasarela-guest"', false)
            ->assertSee('Proceder a Pasarela Notariada');

        // 2. Como usuario autenticado, se renderiza el enlace directo
        $cliente = Usuario::whereHas('roles', fn ($q) => $q->where('nombre', 'cliente'))->firstOrFail();
        $resAuth = $this->actingAs($cliente)->get(route('public.home'));
        $resAuth->assertOk()
            ->assertDontSee('id="btn-proceder-pasarela-guest"', false)
            ->assertSee(route('checkout.index'));
    }
}
