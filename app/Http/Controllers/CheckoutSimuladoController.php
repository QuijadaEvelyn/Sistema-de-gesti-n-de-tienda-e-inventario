<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Carrito;
use App\Models\CarritoItem;
use App\Models\Cliente;
use App\Models\DetalleEnvio;
use App\Models\DetallePago;
use App\Models\DetallePedido;
use App\Models\Direccion;
use App\Models\Envio;
use App\Models\Existencia;
use App\Models\HistorialEnvio;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Sucursal;
use App\Models\VarianteSku;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutSimuladoController extends Controller
{
    /**
     * Etapas del rastreo interactivo de paquete de ARCHIVUM.
     *
     * @var array<string, array{paso: int, titulo: string, ubicacion: string, descripcion: string, icono: string}>
     */
    public const ETAPAS_RASTREO = [
        'en_preparacion' => [
            'paso' => 1,
            'titulo' => 'Bóveda Preparando Lote',
            'ubicacion' => 'Bóveda Central Bellas Artes, CDMX',
            'descripcion' => 'Bóveda Preparando Lote: Verificación pericial de autenticidad, control higrométrico y liberación de custodia física.',
            'icono' => 'inventory_2',
        ],
        'en_transito' => [
            'paso' => 2,
            'titulo' => 'Empaque Notariado',
            'ubicacion' => 'Cámara de Sellado Antiácido ARCHIVUM, CDMX',
            'descripcion' => 'Empaque Notariado: Encapsulado libre de ácido con lacre de seguridad y acta de integridad física firmada.',
            'icono' => 'verified',
        ],
        'en_camino' => [
            'paso' => 3,
            'titulo' => 'En Tránsito con Custodia',
            'ubicacion' => 'Unidad de Transporte Climatizado en Ruta',
            'descripcion' => 'En Tránsito con Custodia: Traslado monitoreado por GPS con temperatura controlada (18 °C / 45 % HR) hacia el destino.',
            'icono' => 'local_shipping',
        ],
        'entregado' => [
            'paso' => 4,
            'titulo' => 'Entregado en Sede / Domicilio',
            'ubicacion' => 'Domicilio / Sede del Coleccionista',
            'descripcion' => 'Entregado en Sede / Domicilio: Recepción directa confirmada con verificación de sellos intactos y entrega de COA.',
            'icono' => 'where_to_vote',
        ],
    ];

    /**
     * Obtiene el estado actual del carrito (combinando sesión y BD si aplica).
     *
     * @return array{items: list<array{variante_sku_id: int, sku: string, nombre: string, subtitulo: string, graduacion: string, tipo: string, precio: float, cantidad: int, subtotal: float}>, subtotal: float, total: float, cantidad_total: int}
     */
    public static function obtenerEstadoCarrito(): array
    {
        if (! Schema::hasTable('variantes_sku')) {
            return [
                'items' => [],
                'subtotal' => 0.0,
                'total' => 0.0,
                'cantidad_total' => 0,
            ];
        }

        /** @var array<int|string, int> $mapaCantidades */
        $mapaCantidades = session('carrito_archivum', []);

        // Si el usuario autenticado tiene un carrito en BD y la sesión está vacía, hidratar desde BD
        if (empty($mapaCantidades) && auth()->check()) {
            $cliente = Cliente::where('usuario_id', auth()->id())->first();
            if ($cliente) {
                $carritoBd = Carrito::where('cliente_id', $cliente->id)->first();
                if ($carritoBd) {
                    foreach ($carritoBd->items as $dbItem) {
                        $mapaCantidades[(int) $dbItem->variante_sku_id] = (int) $dbItem->cantidad;
                    }
                    if (! empty($mapaCantidades)) {
                        session(['carrito_archivum' => $mapaCantidades]);
                    }
                }
            }
        }

        if (empty($mapaCantidades)) {
            return [
                'items' => [],
                'subtotal' => 0.0,
                'total' => 0.0,
                'cantidad_total' => 0,
            ];
        }

        $variantes = VarianteSku::with(['producto.catalogo', 'producto.libro', 'producto.disco', 'producto.coleccionable'])
            ->whereIn('id', array_keys($mapaCantidades))
            ->get()
            ->keyBy('id');

        $items = [];
        $subtotal = 0.0;
        $cantidadTotal = 0;

        foreach ($mapaCantidades as $varianteId => $cantidad) {
            /** @var VarianteSku|null $variante */
            $variante = $variantes->get((int) $varianteId);
            if (! $variante || ! $variante->producto) {
                continue;
            }

            $producto = $variante->producto;
            $precio = (float) ($variante->precio ?? $producto->precio);
            $lineaSubtotal = $precio * (int) $cantidad;
            $subtotal += $lineaSubtotal;
            $cantidadTotal += (int) $cantidad;

            $subtitulo = $producto->libro?->autor
                ?? $producto->disco?->artista
                ?? $producto->coleccionable?->marca
                ?? 'Acervo Certificado ARCHIVUM';

            $items[] = [
                'variante_sku_id' => (int) $variante->id,
                'sku' => $variante->sku,
                'nombre' => $producto->nombre,
                'subtitulo' => $subtitulo,
                'graduacion' => $variante->descripcion ?? 'MINT (M)',
                'tipo' => $producto->catalogo?->tipo ?? 'coleccionable',
                'precio' => $precio,
                'cantidad' => (int) $cantidad,
                'subtotal' => $lineaSubtotal,
            ];
        }

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'cantidad_total' => $cantidadTotal,
        ];
    }

    /**
     * Agrega una pieza al carrito de adquisiciones.
     */
    public function agregar(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'variante_sku_id' => ['required', 'integer', 'exists:variantes_sku,id'],
            'cantidad' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $varianteId = (int) $validated['variante_sku_id'];
        $cantidad = (int) ($validated['cantidad'] ?? 1);

        /** @var array<int, int> $carrito */
        $carrito = session('carrito_archivum', []);
        $carrito[$varianteId] = ($carrito[$varianteId] ?? 0) + $cantidad;
        session(['carrito_archivum' => $carrito]);

        $this->sincronizarCarritoBaseDatos($carrito);

        $variante = VarianteSku::with('producto')->find($varianteId);
        $nombrePieza = $variante?->producto?->nombre ?? 'Pieza seleccionada';

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "«{$nombrePieza}» agregada a tu Bóveda de Adquisición.",
                'carrito' => self::obtenerEstadoCarrito(),
            ]);
        }

        return back()
            ->with('status', "«{$nombrePieza}» se agregó a tu Bóveda de Adquisición.")
            ->with('abrir_carrito', true);
    }

    /**
     * Elimina un artículo del carrito de adquisiciones.
     */
    public function remover(Request $request, int $varianteSkuId): RedirectResponse|JsonResponse
    {
        /** @var array<int, int> $carrito */
        $carrito = session('carrito_archivum', []);
        unset($carrito[$varianteSkuId]);
        session(['carrito_archivum' => $carrito]);

        $this->sincronizarCarritoBaseDatos($carrito);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'carrito' => self::obtenerEstadoCarrito(),
            ]);
        }

        return back()->with('status', 'Pieza liberada de la Bóveda de Adquisición.');
    }

    /**
     * Muestra la pantalla de Checkout y Pasarela de Pago Simulada.
     */
    public function checkout(): View
    {
        $estadoCarrito = self::obtenerEstadoCarrito();

        return view('public.checkout', [
            'carrito' => $estadoCarrito,
            'usuario' => auth()->user(),
        ]);
    }

    /**
     * Procesa la compra con la Pasarela de Pago Simulada (acepta cualquier número de tarjeta
     * en modo prueba/simulación), genera el Pedido, el Pago aprobado y el Envío con rastreo.
     */
    public function procesarPago(Request $request): RedirectResponse
    {
        $estadoCarrito = self::obtenerEstadoCarrito();

        if (empty($estadoCarrito['items'])) {
            return redirect()->route('public.home')->with('warning', 'Tu Bóveda de Adquisición está vacía. Selecciona al menos una pieza antes de continuar.');
        }

        $validated = $request->validate([
            'nombre_completo' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:25'],
            'calle' => ['required', 'string', 'max:200'],
            'colonia' => ['nullable', 'string', 'max:120'],
            'ciudad' => ['required', 'string', 'max:120'],
            'estado' => ['required', 'string', 'max:120'],
            'codigo_postal' => ['required', 'string', 'max:15'],
            'nombre_titular' => ['required', 'string', 'max:150'],
            // En modo simulación acepta cualquier número de tarjeta ingresado por el usuario
            'numero_tarjeta' => ['required', 'string', 'min:4', 'max:35'],
            'expiracion' => ['required', 'string', 'max:10'],
            'cvv' => ['required', 'string', 'min:3', 'max:4'],
        ]);

        /** @var Pedido $pedido */
        $pedido = DB::transaction(function () use ($validated, $estadoCarrito, $request): Pedido {
            $partesNombre = explode(' ', trim($validated['nombre_completo']), 2);
            $nombre = $partesNombre[0];
            $apellidos = $partesNombre[1] ?? 'Coleccionista';

            // 1. Resolver o crear el Cliente
            $cliente = null;
            if (auth()->check()) {
                $cliente = Cliente::where('usuario_id', auth()->id())->first();
            }
            if (! $cliente) {
                $cliente = Cliente::firstOrCreate(
                    ['email' => Str::lower($validated['email'])],
                    [
                        'usuario_id' => auth()->id(),
                        'nombre' => $nombre,
                        'apellidos' => $apellidos,
                        'telefono' => $validated['telefono'] ?? null,
                    ]
                );
            }

            // 2. Registrar Dirección de entrega
            $direccion = Direccion::create([
                'cliente_id' => $cliente->id,
                'alias' => 'Entrega Custodiada',
                'calle' => $validated['calle'],
                'numero_exterior' => 'S/N',
                'colonia' => $validated['colonia'] ?? 'Centro',
                'municipio' => $validated['ciudad'],
                'estado' => $validated['estado'],
                'codigo_postal' => $validated['codigo_postal'],
                'pais' => 'México',
                'predeterminada' => true,
            ]);

            // 3. Crear Pedido pagado
            $folio = 'ARC-'.now()->format('Ymd').'-'.strtoupper(Str::random(5));
            $pedido = Pedido::create([
                'folio' => $folio,
                'cliente_id' => $cliente->id,
                'direccion_envio_id' => $direccion->id,
                'estatus' => 'pagado',
                'subtotal' => $estadoCarrito['subtotal'],
                'descuento' => 0,
                'total' => $estadoCarrito['total'],
            ]);

            $sucursalOrigen = Sucursal::where('activa', true)->first();

            // 4. Crear DetallePedido y descontar stock si existe
            foreach ($estadoCarrito['items'] as $linea) {
                DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'variante_sku_id' => $linea['variante_sku_id'],
                    'cantidad' => $linea['cantidad'],
                    'precio_unitario' => $linea['precio'],
                    'subtotal' => $linea['subtotal'],
                ]);

                $existencia = Existencia::where('variante_sku_id', $linea['variante_sku_id'])
                    ->where('cantidad', '>=', $linea['cantidad'])
                    ->lockForUpdate()
                    ->first();

                if ($existencia) {
                    $existencia->decrement('cantidad', $linea['cantidad']);
                }
            }

            // 5. Registrar Pago aprobado en modo simulación con cualquier tarjeta ingresada
            $digitosLimpios = preg_replace('/\D/', '', $validated['numero_tarjeta']) ?: '4242';
            $ultimosCuatro = substr(str_pad($digitosLimpios, 4, '0', STR_PAD_LEFT), -4);
            $codigoAutorizacion = 'SIM-AUTH-'.random_int(100000, 999999);
            $referenciaPasarela = 'PAS-ARC-'.strtoupper(Str::random(8));

            $pago = Pago::create([
                'pedido_id' => $pedido->id,
                'metodo' => 'tarjeta',
                'metodo_pago' => 'tarjeta',
                'monto' => $estadoCarrito['total'],
                'estatus' => 'aprobado',
                'referencia_bancaria' => $referenciaPasarela,
                'pagado_en' => now(),
            ]);

            DetallePago::create([
                'pago_id' => $pago->id,
                'referencia_externa' => $referenciaPasarela,
                'ultimos_digitos' => $ultimosCuatro,
                'autorizacion' => $codigoAutorizacion,
                'respuesta_pasarela' => [
                    'modo' => 'SIMULACION_PRUEBA',
                    'titular' => $validated['nombre_titular'],
                    'terminacion_tarjeta' => $ultimosCuatro,
                    'codigo_autorizacion' => $codigoAutorizacion,
                    'mensaje' => 'Transacción aprobada satisfactoriamente por Pasarela Simulada ARCHIVUM.',
                ],
            ]);

            // 6. Inicializar el Envío y el primer hito de seguimiento ("Bóveda Preparando Lote")
            $guia = 'GUA-ARC-'.now()->format('ymd').'-'.strtoupper(Str::random(6));
            $envio = Envio::create([
                'pedido_id' => $pedido->id,
                'sucursal_id' => $sucursalOrigen?->id,
                'paqueteria' => 'Custodia Climatizada ARCHIVUM',
                'numero_guia' => $guia,
                'estado_actual' => 'en_preparacion',
                'estatus' => 'preparando',
                'fecha_estimada_entrega' => now()->addDays(3),
            ]);

            foreach ($pedido->detalles as $detallePedido) {
                DetalleEnvio::create([
                    'envio_id' => $envio->id,
                    'detalle_pedido_id' => $detallePedido->id,
                    'cantidad' => $detallePedido->cantidad,
                ]);
            }

            $etapaInicial = self::ETAPAS_RASTREO['en_preparacion'];
            HistorialEnvio::create([
                'envio_id' => $envio->id,
                'estado' => 'en_preparacion',
                'ubicacion' => $etapaInicial['ubicacion'],
                'descripcion' => $etapaInicial['descripcion'],
                'created_at' => now(),
            ]);

            Bitacora::create([
                'usuario_id' => auth()->id(),
                'accion' => 'CHECKOUT_SIMULADO_EXITOSO',
                'tabla_afectada' => 'pedidos',
                'registro_id' => $pedido->id,
                'detalle' => [
                    'folio' => $pedido->folio,
                    'total' => $pedido->total,
                    'ultimos_digitos' => $ultimosCuatro,
                    'numero_guia' => $guia,
                ],
                'ip' => $request->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);

            return $pedido;
        });

        // 7. Limpiar el carrito y guardar el pedido en la sesión del cliente
        session()->forget('carrito_archivum');
        $this->sincronizarCarritoBaseDatos([]);

        $pedidosRecientes = session('pedidos_recientes', []);
        $pedidosRecientes[] = $pedido->id;
        session(['pedidos_recientes' => array_values(array_unique($pedidosRecientes))]);

        return redirect()
            ->route('checkout.confirmacion', $pedido)
            ->with('pago_exitoso', true)
            ->with('status', "¡Pago Exitoso! Tu adquisición con folio {$pedido->folio} ha sido aprobada y registrada.");
    }

    /**
     * Muestra la pantalla de confirmación de pago exitoso y el panel interactivo
     * de seguimiento del paquete (rastreo de envío en tiempo real).
     */
    public function confirmacion(Pedido $pedido): View
    {
        $pedido->load([
            'cliente',
            'direccionEnvio',
            'detalles.varianteSku.producto',
            'pagos.detalles',
            'envios.historial' => fn ($q) => $q->orderBy('created_at')->orderBy('id'),
        ]);

        return view('public.confirmacion-pedido', [
            'pedido' => $pedido,
            'envio' => $pedido->envios->first(),
            'pago' => $pedido->pagos->first(),
            'etapasRastreo' => self::ETAPAS_RASTREO,
        ]);
    }

    /**
     * Simula interactivamente el avance del paquete a la siguiente etapa (o a una etapa específica)
     * para que el cliente pueda visualizar el flujo completo desde su panel.
     */
    public function simularAvanceEnvio(Request $request, Pedido $pedido): RedirectResponse
    {
        /** @var Envio|null $envio */
        $envio = $pedido->envios()->first();

        if (! $envio) {
            return back()->with('warning', 'Este pedido no cuenta con una guía de envío asociada.');
        }

        $ordenEtapas = array_keys(self::ETAPAS_RASTREO);
        $etapaSolicitada = $request->string('etapa')->toString();

        if ($etapaSolicitada !== '' && isset(self::ETAPAS_RASTREO[$etapaSolicitada])) {
            $nuevaEtapa = $etapaSolicitada;
        } else {
            $indiceActual = array_search($envio->estado_actual, $ordenEtapas, true);
            $siguienteIndice = ($indiceActual === false) ? 0 : min($indiceActual + 1, count($ordenEtapas) - 1);
            $nuevaEtapa = $ordenEtapas[$siguienteIndice];
        }

        $metaEtapa = self::ETAPAS_RASTREO[$nuevaEtapa];

        DB::transaction(function () use ($pedido, $envio, $nuevaEtapa, $metaEtapa): void {
            $estatusEnvio = match ($nuevaEtapa) {
                'en_preparacion' => 'preparando',
                'en_transito', 'en_camino' => 'en_transito',
                'entregado' => 'entregado',
                default => 'preparando',
            };

            $envio->update([
                'estado_actual' => $nuevaEtapa,
                'estatus' => $estatusEnvio,
                'enviado_en' => in_array($nuevaEtapa, ['en_transito', 'en_camino', 'entregado'], true) ? ($envio->enviado_en ?? now()) : null,
                'entregado_en' => $nuevaEtapa === 'entregado' ? now() : null,
            ]);

            $pedido->update([
                'estatus' => $nuevaEtapa === 'entregado' ? 'entregado' : ($nuevaEtapa === 'en_preparacion' ? 'pagado' : 'enviado'),
            ]);

            HistorialEnvio::create([
                'envio_id' => $envio->id,
                'estado' => $nuevaEtapa,
                'ubicacion' => $metaEtapa['ubicacion'],
                'descripcion' => $metaEtapa['descripcion'],
                'created_at' => now(),
            ]);
        });

        return back()->with('status', "Estado del envío actualizado a: «{$metaEtapa['titulo']}».");
    }

    /**
     * Panel del cliente: historial de adquisiciones y seguimiento interactivo de envíos.
     */
    public function misPedidos(): View
    {
        $pedidos = collect();

        if (Schema::hasTable('pedidos')) {
            $idsSesion = session('pedidos_recientes', []);
            $clienteId = null;

            if (auth()->check()) {
                $clienteId = Cliente::where('usuario_id', auth()->id())->value('id');
            }

            if ($clienteId || ! empty($idsSesion)) {
                $pedidos = Pedido::with(['detalles.varianteSku.producto', 'envios.historial', 'pagos'])
                    ->where(function ($q) use ($clienteId, $idsSesion): void {
                        if ($clienteId) {
                            $q->where('cliente_id', $clienteId);
                        }
                        if (! empty($idsSesion)) {
                            $q->orWhereIn('id', $idsSesion);
                        }
                    })
                    ->latest()
                    ->get();
            }
        }

        return view('public.mis-pedidos', [
            'pedidos' => $pedidos,
            'etapasRastreo' => self::ETAPAS_RASTREO,
        ]);
    }

    /**
     * Sincroniza el arreglo del carrito en sesión con la tabla `carrito_items` si el usuario tiene perfil de Cliente.
     *
     * @param  array<int, int>  $carritoSesion
     */
    private function sincronizarCarritoBaseDatos(array $carritoSesion): void
    {
        if (! auth()->check() || ! Schema::hasTable('carritos')) {
            return;
        }

        $cliente = Cliente::where('usuario_id', auth()->id())->first();
        if (! $cliente) {
            return;
        }

        $carritoBd = Carrito::firstOrCreate(['cliente_id' => $cliente->id]);
        CarritoItem::where('carrito_id', $carritoBd->id)->delete();

        foreach ($carritoSesion as $varianteSkuId => $cantidad) {
            CarritoItem::create([
                'carrito_id' => $carritoBd->id,
                'variante_sku_id' => (int) $varianteSkuId,
                'cantidad' => (int) $cantidad,
            ]);
        }
    }
}
