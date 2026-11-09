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
use App\Models\Devolucion;
use App\Models\Direccion;
use App\Models\Envio;
use App\Models\Existencia;
use App\Models\HistorialEnvio;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\PiezaUnica;
use App\Models\Producto;
use App\Models\ReservaStock;
use App\Models\Sucursal;
use App\Models\VarianteSku;
use App\Services\DevolucionService;
use App\Services\PreventaService;
use App\Services\ReservaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class CheckoutSimuladoController extends Controller
{
    /**
     * Duración estricta del temporizador de reserva temporal en carrito (5 minutos).
     */
    public const MINUTOS_EXPIRACION_RESERVA = 5;

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
     * Libera automáticamente cualquier reserva de stock cuyo temporizador de 5 minutos
     * haya expirado, retornando las cantidades a la tabla `existencias` y limpiando
     * el carrito en sesión y base de datos si corresponde.
     */
    public static function liberarReservasExpiradas(): int
    {
        if (! Schema::hasTable('reservas_stock') || ! Schema::hasTable('existencias')) {
            return 0;
        }

        $liberadas = app(ReservaService::class)->liberarReservasVencidas();

        // Sincronizar la sesión actual si tenía reservas que acaban de expirar
        /** @var list<int> $idsReservasSesion */
        $idsReservasSesion = array_values(array_map('intval', (array) session('carrito_reservas_ids', [])));
        $expiraSesionStr = session('carrito_expira_en');

        if (! empty($idsReservasSesion)) {
            $reservasActivas = ReservaStock::with('existencia')
                ->whereIn('id', $idsReservasSesion)
                ->where('estado', 'activa')
                ->get();

            if ($reservasActivas->isEmpty()) {
                session()->forget(['carrito_archivum', 'carrito_reservas_ids', 'carrito_expira_en']);
                session()->flash(
                    'warning',
                    'El temporizador de reserva de 5 minutos ha expirado por inactividad. Las piezas han sido liberadas y devueltas a la bóveda.'
                );
            } elseif ($reservasActivas->count() < count($idsReservasSesion)) {
                $nuevoMapa = [];
                $nuevosIds = [];
                foreach ($reservasActivas as $resActiva) {
                    if ($resActiva->existencia) {
                        $varId = (int) $resActiva->existencia->variante_sku_id;
                        $nuevoMapa[$varId] = ($nuevoMapa[$varId] ?? 0) + (int) $resActiva->cantidad;
                        $nuevosIds[] = (int) $resActiva->id;
                    }
                }
                session([
                    'carrito_archivum' => $nuevoMapa,
                    'carrito_reservas_ids' => $nuevosIds,
                ]);
            }
        } elseif (is_string($expiraSesionStr) && Carbon::parse($expiraSesionStr)->lte(now())) {
            session()->forget(['carrito_archivum', 'carrito_reservas_ids', 'carrito_expira_en']);
        }

        return $liberadas;
    }

    /**
     * Obtiene el estado actual del carrito (combinando sesión, reservas activas y temporizador de 5 minutos).
     *
     * @return array{items: list<array{variante_sku_id: int, sku: string, nombre: string, subtitulo: string, graduacion: string, tipo: string, es_preventa: bool, limite_preventa: int|null, precio: float, cantidad: int, subtotal: float}>, subtotal: float, total: float, cantidad_total: int, expira_en_iso: string|null, expira_en_ms: int, segundos_restantes: int, tiempo_formateado: string}
     */
    public static function obtenerEstadoCarrito(): array
    {
        if (! Schema::hasTable('variantes_sku')) {
            return [
                'items' => [],
                'subtotal' => 0.0,
                'total' => 0.0,
                'cantidad_total' => 0,
                'expira_en_iso' => null,
                'expira_en_ms' => 0,
                'segundos_restantes' => 0,
                'tiempo_formateado' => '05:00 min',
            ];
        }

        self::liberarReservasExpiradas();

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
                'expira_en_iso' => null,
                'expira_en_ms' => 0,
                'segundos_restantes' => 0,
                'tiempo_formateado' => '05:00 min',
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

            $esPreventa = self::esPiezaPreventa($producto, $variante);

            $items[] = [
                'variante_sku_id' => (int) $variante->id,
                'sku' => $variante->sku,
                'nombre' => $producto->nombre,
                'subtitulo' => $subtitulo,
                'graduacion' => $variante->descripcion ?? 'MINT (M)',
                'tipo' => $producto->catalogo?->tipo ?? 'coleccionable',
                'es_preventa' => $esPreventa,
                'limite_preventa' => $producto->limite_preventa,
                'precio' => $precio,
                'cantidad' => (int) $cantidad,
                'subtotal' => $lineaSubtotal,
            ];
        }

        $expiraEnIso = session('carrito_expira_en');
        $expiraCarbon = is_string($expiraEnIso)
            ? Carbon::parse($expiraEnIso)
            : now()->addMinutes(self::MINUTOS_EXPIRACION_RESERVA);

        $segundosRestantes = max(0, (int) now()->diffInSeconds($expiraCarbon, false));
        $minutos = intdiv($segundosRestantes, 60);
        $segundos = $segundosRestantes % 60;
        $tiempoFormateado = sprintf('%02d:%02d min', $minutos, $segundos);

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'cantidad_total' => $cantidadTotal,
            'expira_en_iso' => $expiraCarbon->toIso8601String(),
            'expira_en_ms' => $expiraCarbon->getTimestamp() * 1000,
            'segundos_restantes' => $segundosRestantes,
            'tiempo_formateado' => $tiempoFormateado,
        ];
    }

    /**
     * Determina si un producto o variante está catalogado bajo régimen de Preventa.
     */
    public static function esPiezaPreventa(Producto $producto, ?VarianteSku $variante = null): bool
    {
        if ((bool) ($producto->es_preventa ?? false)) {
            return true;
        }

        if (($producto->limite_preventa ?? null) !== null && (int) $producto->limite_preventa > 0) {
            return true;
        }

        if (stripos($producto->nombre, 'PREVENTA') !== false || stripos((string) $producto->descripcion, 'PREVENTA') !== false) {
            return true;
        }

        if ($variante && stripos((string) $variante->descripcion, 'PREVENTA') !== false) {
            return true;
        }

        return false;
    }

    /**
     * Agrega una pieza al carrito de adquisiciones:
     * 1. Libera reservas expiradas previamente.
     * 2. Valida cupo estricto de preventa (si aplica) y stock disponible en bóveda.
     * 3. Descuenta inmediatamente el stock en `existencias` de forma temporal y crea
     *    el registro en `reservas_stock` con temporizador de 5 minutos.
     */
    public function agregar(Request $request): RedirectResponse|JsonResponse
    {
        self::liberarReservasExpiradas();

        $validated = $request->validate([
            'variante_sku_id' => ['required', 'integer', 'exists:variantes_sku,id'],
            'cantidad' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $varianteId = (int) $validated['variante_sku_id'];
        $cantidad = (int) ($validated['cantidad'] ?? 1);

        $variante = VarianteSku::with(['producto.variantesSku', 'existencias'])->findOrFail($varianteId);
        $producto = $variante->producto;
        $nombrePieza = $producto?->nombre ?? 'Pieza seleccionada';

        /** @var array<int, int> $carrito */
        $carrito = session('carrito_archivum', []);
        $cantidadActualEnCarrito = (int) ($carrito[$varianteId] ?? 0);

        // 1. Control estricto en Preventas: bloquear sobreventa si se supera el lote autorizado por Compras
        if ($producto && self::esPiezaPreventa($producto, $variante)) {
            try {
                app(PreventaService::class)->validarCupoPreventa($producto->id, $cantidadActualEnCarrito + $cantidad);
            } catch (RuntimeException $e) {
                $mensajePreventa = $e->getMessage();

                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $mensajePreventa,
                    ], 422);
                }

                return back()
                    ->with('error', $mensajePreventa)
                    ->withErrors(['stock' => $mensajePreventa]);
            }
        }

        // 2. Validar stock disponible en bóveda
        // Como cada reserva activa ya descontó de `existencias`, la suma actual en `existencias`
        // representa las unidades libres adicionales que aún quedan en bóveda.
        $stockLibreEnBoveda = (int) $variante->existencias->sum('cantidad');

        if ($stockLibreEnBoveda <= 0) {
            $mensajeAgotado = $producto && self::esPiezaPreventa($producto, $variante)
                ? "Sobreventa bloqueada: El lote autorizado de preventa para «{$nombrePieza}» se encuentra AGOTADO y no se encuentra disponible en bóveda."
                : "Operación bloqueada: El artículo «{$nombrePieza}» se encuentra AGOTADO y no se encuentra disponible en bóveda.";

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $mensajeAgotado,
                ], 422);
            }

            return back()
                ->with('error', $mensajeAgotado)
                ->withErrors(['stock' => $mensajeAgotado]);
        }

        if ($cantidad > $stockLibreEnBoveda) {
            $mensajeInsuficiente = $producto && self::esPiezaPreventa($producto, $variante)
                ? "Sobreventa bloqueada: La cantidad solicitada supera las unidades previstas por compras disponibles en bóveda ({$stockLibreEnBoveda}) para «{$nombrePieza}»."
                : "Operación bloqueada: No hay suficientes ejemplares de «{$nombrePieza}» disponibles en bóveda (stock actual: {$stockLibreEnBoveda}).";

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $mensajeInsuficiente,
                ], 422);
            }

            return back()
                ->with('error', $mensajeInsuficiente)
                ->withErrors(['stock' => $mensajeInsuficiente]);
        }

        $expiraEn = now()->addMinutes(self::MINUTOS_EXPIRACION_RESERVA);
        /** @var list<int> $idsReservasSesion */
        $idsReservasSesion = array_values(array_map('intval', (array) session('carrito_reservas_ids', [])));

        // 3. Disminución inmediata del stock en base de datos y creación de ReservaStock por 5 minutos
        DB::transaction(function () use ($varianteId, $cantidad, $expiraEn, &$idsReservasSesion): void {
            $carritoBd = $this->obtenerOCrearCarritoBaseDatos();
            $porDescontar = $cantidad;

            $existencias = Existencia::where('variante_sku_id', $varianteId)
                ->where('cantidad', '>', 0)
                ->orderByDesc('cantidad')
                ->lockForUpdate()
                ->get();

            $totalBloqueado = (int) $existencias->sum('cantidad');
            if ($totalBloqueado < $cantidad) {
                throw new RuntimeException('El artículo acaba de agotarse en bóveda.');
            }

            foreach ($existencias as $existencia) {
                if ($porDescontar <= 0) {
                    break;
                }

                $descuentoSucursal = min((int) $existencia->cantidad, $porDescontar);
                $existencia->decrement('cantidad', $descuentoSucursal);
                $porDescontar -= $descuentoSucursal;

                $piezaUnica = PiezaUnica::where('existencia_id', $existencia->id)
                    ->where('estado', 'disponible')
                    ->lockForUpdate()
                    ->first();

                if ($piezaUnica) {
                    $piezaUnica->update(['estado' => 'reservada']);
                }

                $reserva = ReservaStock::create([
                    'existencia_id' => $existencia->id,
                    'pedido_id' => null,
                    'carrito_id' => $carritoBd?->id,
                    'cantidad' => $descuentoSucursal,
                    'expira_en' => $expiraEn,
                    'estado' => 'activa',
                ]);

                $idsReservasSesion[] = (int) $reserva->id;
            }

            // Actualizar la vigencia de todas las reservas activas de este carrito a los nuevos 5 minutos
            if (! empty($idsReservasSesion)) {
                ReservaStock::whereIn('id', $idsReservasSesion)
                    ->where('estado', 'activa')
                    ->update(['expira_en' => $expiraEn]);
            }
        });

        $carrito[$varianteId] = $cantidadActualEnCarrito + $cantidad;
        session([
            'carrito_archivum' => $carrito,
            'carrito_reservas_ids' => array_values(array_unique($idsReservasSesion)),
            'carrito_expira_en' => $expiraEn->toIso8601String(),
        ]);

        $this->sincronizarCarritoBaseDatos($carrito);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "«{$nombrePieza}» reservada temporalmente por 5 minutos en tu Bóveda de Adquisición.",
                'carrito' => self::obtenerEstadoCarrito(),
            ]);
        }

        return back()
            ->with('status', "«{$nombrePieza}» se reservó en tu Bóveda de Adquisición (reserva activa por 5:00 min).")
            ->with('abrir_carrito', true);
    }

    /**
     * Elimina un artículo del carrito de adquisiciones y libera de inmediato
     * su stock reservado de vuelta a la tabla `existencias`.
     */
    public function remover(Request $request, int $varianteSkuId): RedirectResponse|JsonResponse
    {
        self::liberarReservasExpiradas();

        /** @var array<int, int> $carrito */
        $carrito = session('carrito_archivum', []);
        /** @var list<int> $idsReservasSesion */
        $idsReservasSesion = array_values(array_map('intval', (array) session('carrito_reservas_ids', [])));

        DB::transaction(function () use ($varianteSkuId, &$idsReservasSesion): void {
            $carritoBd = $this->obtenerOCrearCarritoBaseDatos();

            $reservasLiberar = ReservaStock::with('existencia')
                ->where('estado', 'activa')
                ->whereHas('existencia', fn ($q) => $q->where('variante_sku_id', $varianteSkuId))
                ->where(function ($q) use ($idsReservasSesion, $carritoBd): void {
                    if (! empty($idsReservasSesion)) {
                        $q->whereIn('id', $idsReservasSesion);
                    }
                    if ($carritoBd) {
                        $q->orWhere('carrito_id', $carritoBd->id);
                    }
                })
                ->lockForUpdate()
                ->get();

            $idsLiberados = [];

            foreach ($reservasLiberar as $reserva) {
                $existencia = Existencia::lockForUpdate()->find($reserva->existencia_id);
                if ($existencia) {
                    $existencia->increment('cantidad', $reserva->cantidad);

                    $piezaUnica = PiezaUnica::where('existencia_id', $existencia->id)
                        ->where('estado', 'reservada')
                        ->lockForUpdate()
                        ->first();

                    if ($piezaUnica) {
                        $piezaUnica->update(['estado' => 'disponible']);
                    }
                }

                $reserva->update(['estado' => 'liberada']);
                $idsLiberados[] = (int) $reserva->id;
            }

            if (! empty($idsLiberados)) {
                $idsReservasSesion = array_values(array_diff($idsReservasSesion, $idsLiberados));
            }
        });

        unset($carrito[$varianteSkuId]);
        session([
            'carrito_archivum' => $carrito,
            'carrito_reservas_ids' => $idsReservasSesion,
        ]);

        if (empty($carrito)) {
            session()->forget(['carrito_expira_en', 'carrito_reservas_ids']);
        }

        $this->sincronizarCarritoBaseDatos($carrito);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'carrito' => self::obtenerEstadoCarrito(),
            ]);
        }

        return back()->with('status', 'Pieza liberada de la Bóveda de Adquisición y stock restaurado en catálogo.');
    }

    /**
     * Libera de forma inmediata todas las reservas activas del carrito cuando el
     * temporizador de 5 minutos llega a cero por inactividad.
     */
    public function expirarReservas(Request $request): RedirectResponse|JsonResponse
    {
        /** @var list<int> $idsReservasSesion */
        $idsReservasSesion = array_values(array_map('intval', (array) session('carrito_reservas_ids', [])));

        DB::transaction(function () use ($idsReservasSesion): void {
            $carritoBd = $this->obtenerOCrearCarritoBaseDatos();

            if (! empty($idsReservasSesion) || $carritoBd) {
                $reservas = ReservaStock::where('estado', 'activa')
                    ->where(function ($q) use ($idsReservasSesion, $carritoBd): void {
                        if (! empty($idsReservasSesion)) {
                            $q->whereIn('id', $idsReservasSesion);
                        }
                        if ($carritoBd) {
                            $q->orWhere('carrito_id', $carritoBd->id);
                        }
                    })
                    ->lockForUpdate()
                    ->get();

                foreach ($reservas as $reserva) {
                    $existencia = Existencia::lockForUpdate()->find($reserva->existencia_id);
                    if ($existencia) {
                        $existencia->increment('cantidad', $reserva->cantidad);

                        $piezaUnica = PiezaUnica::where('existencia_id', $existencia->id)
                            ->where('estado', 'reservada')
                            ->lockForUpdate()
                            ->first();

                        if ($piezaUnica) {
                            $piezaUnica->update(['estado' => 'disponible']);
                        }
                    }

                    $reserva->update(['estado' => 'expirada']);
                }
            }
        });

        self::liberarReservasExpiradas();

        session()->forget(['carrito_archivum', 'carrito_reservas_ids', 'carrito_expira_en']);
        $this->sincronizarCarritoBaseDatos([]);

        $mensaje = 'El temporizador de 5 minutos ha expirado por inactividad. El stock reservado fue liberado y devuelto al catálogo.';

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'expired',
                'message' => $mensaje,
                'carrito' => self::obtenerEstadoCarrito(),
            ]);
        }

        return redirect()->route('public.home')->with('warning', $mensaje);
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
     * en modo prueba/simulación), confirma las reservas activas de stock, genera el Pedido,
     * el Pago aprobado y el Envío con rastreo.
     */
    public function procesarPago(Request $request): RedirectResponse
    {
        $teniaItemsEnSesion = ! empty(session('carrito_archivum', []));
        $estadoCarrito = self::obtenerEstadoCarrito();

        if (empty($estadoCarrito['items'])) {
            if ($teniaItemsEnSesion) {
                $mensajeExpirado = 'Transacción bloqueada: Tu reserva temporal de 5 minutos ha expirado por inactividad y el stock fue devuelto a la bóveda.';

                return back()
                    ->withInput()
                    ->with('error', $mensajeExpirado)
                    ->withErrors(['stock' => $mensajeExpirado]);
            }

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

        /** @var list<int> $idsReservasSesion */
        $idsReservasSesion = array_values(array_map('intval', (array) session('carrito_reservas_ids', [])));
        $carritoBd = $this->obtenerOCrearCarritoBaseDatos();

        // Validación estricta de disponibilidad de inventario y preventas antes de procesar el pago
        foreach ($estadoCarrito['items'] as $linea) {
            $variante = VarianteSku::with('producto')->find($linea['variante_sku_id']);
            if ($variante?->producto && self::esPiezaPreventa($variante->producto, $variante)) {
                try {
                    app(PreventaService::class)->validarCupoPreventa($variante->producto->id, (int) $linea['cantidad']);
                } catch (RuntimeException $e) {
                    return back()
                        ->withInput()
                        ->with('error', $e->getMessage())
                        ->withErrors(['stock' => $e->getMessage()]);
                }
            }

            $cantidadYaReservada = 0;
            if (! empty($idsReservasSesion) || $carritoBd) {
                $cantidadYaReservada = (int) ReservaStock::where('estado', 'activa')
                    ->whereHas('existencia', fn ($q) => $q->where('variante_sku_id', $linea['variante_sku_id']))
                    ->where(function ($q) use ($idsReservasSesion, $carritoBd): void {
                        if (! empty($idsReservasSesion)) {
                            $q->whereIn('id', $idsReservasSesion);
                        }
                        if ($carritoBd) {
                            $q->orWhere('carrito_id', $carritoBd->id);
                        }
                    })
                    ->sum('cantidad');
            }

            $faltanteSinReserva = max(0, (int) $linea['cantidad'] - $cantidadYaReservada);

            if ($faltanteSinReserva > 0) {
                $stockEnBoveda = (int) Existencia::where('variante_sku_id', $linea['variante_sku_id'])->sum('cantidad');

                if ($stockEnBoveda <= 0 || $stockEnBoveda < $faltanteSinReserva) {
                    /** @var array<int, int> $carritoActual */
                    $carritoActual = session('carrito_archivum', []);
                    $totalRealDisponible = $cantidadYaReservada + max(0, $stockEnBoveda);
                    if ($totalRealDisponible <= 0) {
                        unset($carritoActual[(int) $linea['variante_sku_id']]);
                    } else {
                        $carritoActual[(int) $linea['variante_sku_id']] = $totalRealDisponible;
                    }
                    session(['carrito_archivum' => $carritoActual]);
                    $this->sincronizarCarritoBaseDatos($carritoActual);

                    $mensajeErrorStock = "Transacción bloqueada: El producto «{$linea['nombre']}» no se encuentra disponible en bóveda (stock agotado o insuficiente).";

                    return back()
                        ->withInput()
                        ->with('error', $mensajeErrorStock)
                        ->withErrors(['stock' => $mensajeErrorStock]);
                }
            }
        }

        /** @var Pedido $pedido */
        $pedido = DB::transaction(function () use ($validated, $estadoCarrito, $request, $idsReservasSesion, $carritoBd): Pedido {
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

            // 4. Crear DetallePedido, confirmar reservas activas y descontar cualquier remanente no reservado
            foreach ($estadoCarrito['items'] as $linea) {
                DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'variante_sku_id' => $linea['variante_sku_id'],
                    'cantidad' => $linea['cantidad'],
                    'precio_unitario' => $linea['precio'],
                    'subtotal' => $linea['subtotal'],
                ]);

                $porCubrir = (int) $linea['cantidad'];

                if (! empty($idsReservasSesion) || $carritoBd) {
                    $reservasActivas = ReservaStock::where('estado', 'activa')
                        ->whereHas('existencia', fn ($q) => $q->where('variante_sku_id', $linea['variante_sku_id']))
                        ->where(function ($q) use ($idsReservasSesion, $carritoBd): void {
                            if (! empty($idsReservasSesion)) {
                                $q->whereIn('id', $idsReservasSesion);
                            }
                            if ($carritoBd) {
                                $q->orWhere('carrito_id', $carritoBd->id);
                            }
                        })
                        ->lockForUpdate()
                        ->get();

                    foreach ($reservasActivas as $reserva) {
                        $reserva->update([
                            'pedido_id' => $pedido->id,
                            'estado' => 'confirmada',
                            'expira_en' => null,
                        ]);
                        $porCubrir -= (int) $reserva->cantidad;
                    }
                }

                // Si alguna cantidad no provenía de una reserva previa (ej. sesión inyectada en test), descontar de existencias
                if ($porCubrir > 0) {
                    $existencias = Existencia::where('variante_sku_id', $linea['variante_sku_id'])
                        ->where('cantidad', '>', 0)
                        ->orderByDesc('cantidad')
                        ->lockForUpdate()
                        ->get();

                    foreach ($existencias as $existencia) {
                        if ($porCubrir <= 0) {
                            break;
                        }
                        $descuentoSucursal = min((int) $existencia->cantidad, $porCubrir);
                        $existencia->decrement('cantidad', $descuentoSucursal);
                        $porCubrir -= $descuentoSucursal;
                    }
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

        // 7. Limpiar el carrito y las referencias de reserva temporal en la sesión del cliente
        session()->forget(['carrito_archivum', 'carrito_reservas_ids', 'carrito_expira_en']);
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
            'detalles.devoluciones',
            'devoluciones.detallePedido.varianteSku.producto',
            'pagos.detalles',
            'envios.historial' => fn ($q) => $q->orderBy('created_at')->orderBy('id'),
        ]);

        return view('public.confirmacion-pedido', [
            'pedido' => $pedido,
            'envio' => $pedido->envios->first(),
            'pago' => $pedido->pagos->first(),
            'etapasRastreo' => self::ETAPAS_RASTREO,
            'motivosDevolucion' => Devolucion::MOTIVOS_CATALOGO,
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
                $pedidos = Pedido::with([
                    'detalles.varianteSku.producto',
                    'detalles.devoluciones',
                    'devoluciones.detallePedido.varianteSku.producto',
                    'envios.historial',
                    'pagos',
                ])
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
            'motivosDevolucion' => Devolucion::MOTIVOS_CATALOGO,
        ]);
    }

    /**
     * Registra una solicitud de devolución o garantía enviada por el cliente desde su historial de compras
     * o desde la vista de confirmación/detalle del pedido, adjuntando motivo, comentarios y fotografías de evidencia.
     */
    public function solicitarDevolucion(Request $request, Pedido $pedido): RedirectResponse
    {
        $validated = $request->validate([
            'detalle_pedido_id' => ['required', 'integer', 'exists:detalle_pedidos,id'],
            'cantidad' => ['nullable', 'integer', 'min:1'],
            'motivo' => ['required', 'string', 'max:255'],
            'comentarios_cliente' => ['nullable', 'string', 'max:2000'],
            'evidencias' => ['nullable', 'array', 'max:5'],
            'evidencias.*' => ['file', 'image', 'max:5120'],
            'evidencia_urls' => ['nullable', 'array', 'max:5'],
            'evidencia_urls.*' => ['nullable', 'string', 'max:500'],
        ]);

        $archivosEvidencia = [];
        if ($request->hasFile('evidencias')) {
            $archivosEvidencia = array_merge($archivosEvidencia, (array) $request->file('evidencias'));
        }
        if (! empty($validated['evidencia_urls'])) {
            foreach ($validated['evidencia_urls'] as $url) {
                if (is_string($url) && trim($url) !== '') {
                    $archivosEvidencia[] = trim($url);
                }
            }
        }

        try {
            $devolucion = app(DevolucionService::class)->solicitarDevolucionCliente(
                pedido: $pedido,
                detallePedidoId: (int) $validated['detalle_pedido_id'],
                cantidad: (int) ($validated['cantidad'] ?? 1),
                motivo: (string) $validated['motivo'],
                comentariosCliente: $validated['comentarios_cliente'] ?? null,
                archivosEvidencia: $archivosEvidencia
            );
        } catch (RuntimeException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage())
                ->withErrors(['devolucion' => $e->getMessage()]);
        }

        return back()->with(
            'status',
            "Solicitud de devolución y garantía registrada con folio {$devolucion->folio_devolucion} (Estado: Pendiente de revisión)."
        );
    }

    /**
     * Obtiene o crea el registro `Carrito` en base de datos para el cliente autenticado.
     */
    private function obtenerOCrearCarritoBaseDatos(): ?Carrito
    {
        if (! auth()->check() || ! Schema::hasTable('carritos')) {
            return null;
        }

        $cliente = Cliente::where('usuario_id', auth()->id())->first();
        if (! $cliente) {
            return null;
        }

        return Carrito::firstOrCreate(['cliente_id' => $cliente->id]);
    }

    /**
     * Sincroniza el arreglo del carrito en sesión con la tabla `carrito_items` si el usuario tiene perfil de Cliente.
     *
     * @param  array<int, int>  $carritoSesion
     */
    private function sincronizarCarritoBaseDatos(array $carritoSesion): void
    {
        $carritoBd = $this->obtenerOCrearCarritoBaseDatos();
        if (! $carritoBd) {
            return;
        }

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
