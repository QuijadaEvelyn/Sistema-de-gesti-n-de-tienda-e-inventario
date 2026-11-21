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
use App\Models\Promocion;
use App\Models\ReservaStock;
use App\Models\Sucursal;
use App\Models\Usuario;
use App\Models\VarianteSku;
use App\Services\DevolucionService;
use App\Services\PreventaService;
use App\Services\PromocionDescuentoService;
use App\Services\ReservaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CheckoutSimuladoController extends Controller
{
    /**
     * Duración estricta del temporizador de reserva temporal para Piezas Únicas en carrito (5 minutos).
     */
    public const MINUTOS_EXPIRACION_RESERVA = 5;

    /**
     * Vigencia en el carrito para Productos Comunes / Regulares con stock > 1 (7 días / 1 semana).
     */
    public const DIAS_VIGENCIA_PRODUCTO_COMUN = 7;

    /**
     * Etapas del rastreo interactivo de paquete de ARCHIVUM.
     *
     * @var array<string, array{paso: int, titulo: string, ubicacion: string, descripcion: string, icono: string}>
     */
    public const ETAPAS_RASTREO = [
        'en_preparacion' => [
            'paso' => 1,
            'titulo' => 'Preparando Pedido',
            'ubicacion' => 'Sucursal Central Bellas Artes, CDMX',
            'descripcion' => 'Preparando Pedido: Verificación de calidad, certificado de autenticidad y preparación en almacén.',
            'icono' => 'inventory_2',
        ],
        'en_transito' => [
            'paso' => 2,
            'titulo' => 'Empaque Protegido',
            'ubicacion' => 'Centro de Empaque ARCHIVUM, CDMX',
            'descripcion' => 'Empaque Protegido: Embalaje de alta protección con sello de seguridad e inspección física completada.',
            'icono' => 'verified',
        ],
        'en_camino' => [
            'paso' => 3,
            'titulo' => 'En Camino',
            'ubicacion' => 'Unidad de Envío en Ruta',
            'descripcion' => 'En Camino: Traslado monitoreado hacia la dirección de entrega indicada en tu pedido.',
            'icono' => 'local_shipping',
        ],
        'entregado' => [
            'paso' => 4,
            'titulo' => 'Entregado en Domicilio',
            'ubicacion' => 'Domicilio del Cliente',
            'descripcion' => 'Entregado en Domicilio: Recepción confirmada con entrega de tu producto y Certificado de Autenticidad.',
            'icono' => 'where_to_vote',
        ],
    ];

    /**
     * Libera automáticamente cualquier reserva de stock cuyo tiempo de vigencia haya expirado
     * (5 minutos para Piezas Únicas, 7 días para Productos Comunes), retornando las cantidades
     * a la tabla `existencias` y eliminando del carrito únicamente los artículos vencidos
     * sin afectar los productos comunes vigentes.
     */
    public static function liberarReservasExpiradas(): int
    {
        if (! Schema::hasTable('reservas_stock') || ! Schema::hasTable('existencias')) {
            return 0;
        }

        $liberadas = app(ReservaService::class)->liberarReservasVencidas();

        // Sincronizar la sesión actual conservando intactos los artículos cuyas reservas siguen activas
        /** @var list<int> $idsReservasSesion */
        $idsReservasSesion = array_values(array_map('intval', (array) session('carrito_reservas_ids', [])));
        /** @var array<int|string, int> $carritoActual */
        $carritoActual = (array) session('carrito_archivum', []);

        if (! empty($idsReservasSesion)) {
            $reservasSesion = ReservaStock::with('existencia')
                ->whereIn('id', $idsReservasSesion)
                ->get();

            $reservasActivas = $reservasSesion->where('estado', 'activa');
            $reservasExpiradas = $reservasSesion->whereIn('estado', ['expirada', 'liberada']);

            if ($reservasActivas->isEmpty()) {
                session()->forget(['carrito_archivum', 'carrito_reservas_ids', 'carrito_expira_en']);
                if ($reservasExpiradas->isNotEmpty()) {
                    session()->flash(
                        'warning',
                        'El tiempo de reserva de tus productos reservados ha expirado por inactividad y fueron devueltos al catálogo.'
                    );
                }
            } elseif ($reservasActivas->count() < count($idsReservasSesion) || $reservasExpiradas->isNotEmpty()) {
                $nuevoMapa = [];
                $nuevosIds = [];
                $expiraUnicaMasReciente = null;

                foreach ($reservasActivas as $resActiva) {
                    if ($resActiva->existencia) {
                        $varId = (int) $resActiva->existencia->variante_sku_id;
                        $nuevoMapa[$varId] = ($nuevoMapa[$varId] ?? 0) + (int) $resActiva->cantidad;
                        $nuevosIds[] = (int) $resActiva->id;

                        // Si la reserva activa vence en <= 15 minutos, corresponde a una Pieza Única (5 min)
                        if ($resActiva->expira_en instanceof Carbon && $resActiva->expira_en->lte(now()->addMinutes(15))) {
                            if ($expiraUnicaMasReciente === null || $resActiva->expira_en->gt($expiraUnicaMasReciente)) {
                                $expiraUnicaMasReciente = $resActiva->expira_en;
                            }
                        }
                    }
                }

                session([
                    'carrito_archivum' => $nuevoMapa,
                    'carrito_reservas_ids' => array_values(array_unique($nuevosIds)),
                ]);

                if ($expiraUnicaMasReciente !== null) {
                    session(['carrito_expira_en' => $expiraUnicaMasReciente->toIso8601String()]);
                } else {
                    session()->forget('carrito_expira_en');
                }

                if ($reservasExpiradas->isNotEmpty()) {
                    session()->flash(
                        'warning',
                        'El temporizador de 5 minutos para tus Piezas Únicas ha expirado. Las piezas únicas regresaron al catálogo, mientras que tus productos regulares se conservan guardados por 7 días.'
                    );
                }
            }
        } elseif (! empty($carritoActual)) {
            $expiraSesionStr = session('carrito_expira_en');
            if (is_string($expiraSesionStr) && Carbon::parse($expiraSesionStr)->lte(now())) {
                // Si no había IDs de reserva pero expiró el timestamp de piezas únicas, retirar solo piezas únicas
                $variantesEnSesion = VarianteSku::with(['producto.coleccionable', 'existencias'])
                    ->whereIn('id', array_map('intval', array_keys($carritoActual)))
                    ->get();

                foreach ($variantesEnSesion as $varSesion) {
                    $cant = (int) ($carritoActual[$varSesion->id] ?? 0);
                    if (self::esVariantePiezaUnica($varSesion, $cant)) {
                        unset($carritoActual[$varSesion->id]);
                    }
                }

                session()->forget('carrito_expira_en');
                if (empty($carritoActual)) {
                    session()->forget(['carrito_archivum', 'carrito_reservas_ids']);
                } else {
                    session(['carrito_archivum' => $carritoActual]);
                }
            }
        }

        return $liberadas;
    }

    /**
     * Determina si una variante SKU corresponde a una Pieza Única (registro en `piezas_unicas`,
     * marcada como `edicion_limitada`, o pieza con stock total igual a 1).
     * Las Piezas Únicas tienen reserva temporal de 5 minutos; los Productos Comunes tienen vigencia de 7 días.
     */
    public static function esVariantePiezaUnica(VarianteSku $variante, int $cantidadReservadaEnCarrito = 0): bool
    {
        if (self::obtenerPiezaUnicaDeVariante((int) $variante->id) !== null) {
            return true;
        }

        $producto = $variante->relationLoaded('producto')
            ? $variante->producto
            : $variante->producto()->with('coleccionable')->first();

        if ($producto !== null) {
            $coleccionable = $producto->relationLoaded('coleccionable')
                ? $producto->coleccionable
                : $producto->coleccionable()->first();

            if ((bool) ($coleccionable?->edicion_limitada ?? false)) {
                return true;
            }
        }

        $stockLibre = (int) ($variante->relationLoaded('existencias')
            ? $variante->existencias->sum('cantidad')
            : $variante->existencias()->sum('cantidad'));

        $stockTotal = $stockLibre + max(0, $cantidadReservadaEnCarrito);

        return $stockTotal === 1 && $cantidadReservadaEnCarrito <= 1;
    }

    /**
     * Estructura vacía normalizada del estado del carrito.
     *
     * @return array<string, mixed>
     */
    private static function estadoCarritoVacio(): array
    {
        return [
            'items' => [],
            'items_piezas_unicas' => [],
            'items_regulares' => [],
            'tiene_piezas_unicas' => false,
            'tiene_productos_regulares' => false,
            'tiene_ambos_tipos' => false,
            'cantidad_piezas_unicas' => 0,
            'cantidad_regulares' => 0,
            'subtotal_piezas_unicas' => 0.0,
            'subtotal_regulares' => 0.0,
            'subtotal' => 0.0,
            'total' => 0.0,
            'cantidad_total' => 0,
            'expira_en_iso' => null,
            'expira_en_ms' => 0,
            'segundos_restantes' => 0,
            'tiempo_formateado' => '05:00 min',
            'vigencia_regulares_dias' => self::DIAS_VIGENCIA_PRODUCTO_COMUN,
        ];
    }

    /**
     * Obtiene el estado actual del carrito separando entre Piezas Únicas (reserva de 5 minutos)
     * y Productos Comunes / Regulares (vigencia de 7 días), manteniendo el Total general unificado.
     *
     * @return array<string, mixed>
     */
    public static function obtenerEstadoCarrito(): array
    {
        // Un visitante sin sesión iniciada jamás puede tener artículos precargados o activos en el carrito (0)
        if (! auth()->check() || ! Schema::hasTable('variantes_sku')) {
            return self::estadoCarritoVacio();
        }

        self::liberarReservasExpiradas();

        /** @var array<int|string, int> $mapaCantidades */
        $mapaCantidades = session('carrito_archivum', []);
        /** @var array<int|string, float> $preciosCongelados */
        $preciosCongelados = (array) session('carrito_precios_congelados', []);

        // Sincronizar con la tabla carrito_items (hidratar si la sesión está vacía o recuperar precios congelados en BD)
        if (auth()->check() && Schema::hasTable('clientes') && Schema::hasTable('carritos') && Schema::hasTable('carrito_items')) {
            $cliente = Cliente::where('usuario_id', auth()->id())->first();
            if ($cliente) {
                $carritoBd = Carrito::where('cliente_id', $cliente->id)->first();
                if ($carritoBd) {
                    $hidratarCantidades = empty($mapaCantidades);
                    foreach ($carritoBd->items as $dbItem) {
                        $varIdBd = (int) $dbItem->variante_sku_id;
                        if ($hidratarCantidades) {
                            $mapaCantidades[$varIdBd] = (int) $dbItem->cantidad;
                        }
                        if ($dbItem->precio_unitario_congelado !== null && ! isset($preciosCongelados[$varIdBd])) {
                            $preciosCongelados[$varIdBd] = (float) $dbItem->precio_unitario_congelado;
                        }
                    }
                    if ($hidratarCantidades && ! empty($mapaCantidades)) {
                        session([
                            'carrito_archivum' => $mapaCantidades,
                            'carrito_precios_congelados' => $preciosCongelados,
                        ]);
                        if (Schema::hasTable('reservas_stock')) {
                            $reservasBdIds = ReservaStock::where('carrito_id', $carritoBd->id)
                                ->where('estado', 'activa')
                                ->pluck('id')
                                ->map(fn ($id) => (int) $id)
                                ->all();
                            if (! empty($reservasBdIds)) {
                                session(['carrito_reservas_ids' => $reservasBdIds]);
                            }
                        }
                    } elseif (! empty($preciosCongelados)) {
                        session(['carrito_precios_congelados' => $preciosCongelados]);
                    }
                }
            }
        }

        if (empty($mapaCantidades)) {
            return self::estadoCarritoVacio();
        }

        $variantes = VarianteSku::with(['producto.catalogo', 'producto.libro', 'producto.disco', 'producto.coleccionable', 'existencias'])
            ->whereIn('id', array_keys($mapaCantidades))
            ->get()
            ->keyBy('id');

        /** @var list<int> $idsReservasSesion */
        $idsReservasSesion = array_values(array_map('intval', (array) session('carrito_reservas_ids', [])));
        $reservasPorVariante = collect();
        if (! empty($idsReservasSesion) && Schema::hasTable('reservas_stock')) {
            $reservasPorVariante = ReservaStock::with('existencia')
                ->whereIn('id', $idsReservasSesion)
                ->where('estado', 'activa')
                ->get()
                ->groupBy(fn (ReservaStock $r) => (int) ($r->existencia?->variante_sku_id ?? 0));
        }

        $items = [];
        $itemsPiezasUnicas = [];
        $itemsRegulares = [];
        $subtotal = 0.0;
        $subtotalPiezasUnicas = 0.0;
        $subtotalRegulares = 0.0;
        $cantidadTotal = 0;
        $cantidadPiezasUnicas = 0;
        $cantidadRegulares = 0;
        $expiraUnicaCarbon = null;

        foreach ($mapaCantidades as $varianteId => $cantidad) {
            /** @var VarianteSku|null $variante */
            $variante = $variantes->get((int) $varianteId);
            if (! $variante || ! $variante->producto) {
                continue;
            }

            $producto = $variante->producto;
            $cantidadInt = (int) $cantidad;
            $esPreventa = self::esPiezaPreventa($producto, $variante);
            $esPiezaUnica = self::esVariantePiezaUnica($variante, $cantidadInt);

            // Regla de congelamiento de precio por tipo de producto:
            // - Piezas Únicas: NUNCA llevan precio congelado; siempre reflejan el precio actual ($variante->precio ?? $producto->precio).
            // - Productos Comunes (stock > 1 / catálogo regular): Respetan su `precio_unitario_congelado` guardado al momento de agregarse.
            $precioCatalogoActual = (float) ($variante->precio ?? $producto->precio);
            if ($esPiezaUnica) {
                $precioUnitarioCongelado = null;
                $precio = $precioCatalogoActual;
                unset($preciosCongelados[(int) $variante->id]);
            } else {
                if (isset($preciosCongelados[(int) $variante->id]) && (float) $preciosCongelados[(int) $variante->id] > 0) {
                    $precioUnitarioCongelado = (float) $preciosCongelados[(int) $variante->id];
                } else {
                    $precioUnitarioCongelado = $precioCatalogoActual;
                    $preciosCongelados[(int) $variante->id] = $precioUnitarioCongelado;
                }
                $precio = $precioUnitarioCongelado;
            }

            $lineaSubtotal = $precio * $cantidadInt;
            $subtotal += $lineaSubtotal;
            $cantidadTotal += $cantidadInt;

            $subtitulo = $producto->libro?->autor
                ?? $producto->disco?->artista
                ?? $producto->coleccionable?->marca
                ?? 'Acervo Certificado ARCHIVUM';

            $stockLibre = (int) $variante->existencias->sum('cantidad');
            $maxPosible = $cantidadInt + $stockLibre;
            if ($esPreventa && $producto->limite_preventa) {
                $maxPosible = min($maxPosible, (int) $producto->limite_preventa);
            }
            $maxDisponible = $esPiezaUnica ? 1 : max($cantidadInt, min(20, $maxPosible));

            // Determinar expiración específica de esta línea
            /** @var Collection<int, ReservaStock> $reservasLinea */
            $reservasLinea = $reservasPorVariante->get((int) $variante->id, collect());
            $expiraLineaCarbon = $reservasLinea
                ->pluck('expira_en')
                ->filter(fn ($d) => $d instanceof Carbon)
                ->sortByDesc(fn (Carbon $d) => $d->getTimestamp())
                ->first();

            if (! $expiraLineaCarbon instanceof Carbon) {
                $expiraLineaCarbon = $esPiezaUnica
                    ? (is_string(session('carrito_expira_en')) ? Carbon::parse((string) session('carrito_expira_en')) : now()->addMinutes(self::MINUTOS_EXPIRACION_RESERVA))
                    : now()->addDays(self::DIAS_VIGENCIA_PRODUCTO_COMUN);
            }

            if ($esPiezaUnica) {
                if ($expiraUnicaCarbon === null || $expiraLineaCarbon->gt($expiraUnicaCarbon)) {
                    $expiraUnicaCarbon = $expiraLineaCarbon;
                }
            }

            $itemData = [
                'variante_sku_id' => (int) $variante->id,
                'sku' => $variante->sku,
                'nombre' => $producto->nombre,
                'subtitulo' => $subtitulo,
                'graduacion' => $variante->descripcion ?? 'MINT (M)',
                'tipo' => $producto->catalogo?->tipo ?? 'coleccionable',
                'es_preventa' => $esPreventa,
                'limite_preventa' => $producto->limite_preventa,
                'precio' => $precio,
                'precio_unitario_congelado' => $precioUnitarioCongelado,
                'precio_catalogo_actual' => $precioCatalogoActual,
                'tiene_precio_congelado' => ! $esPiezaUnica,
                'cambio_precio_catalogo' => ! $esPiezaUnica && abs($precioCatalogoActual - $precio) >= 0.01,
                'cantidad' => $cantidadInt,
                'subtotal' => $lineaSubtotal,
                'stock_libre' => $stockLibre,
                'disponibles_adicionales' => $stockLibre,
                'es_pieza_unica' => $esPiezaUnica,
                'vigencia_tipo' => $esPiezaUnica ? '5_minutos' : '7_dias',
                'vigencia_etiqueta' => $esPiezaUnica ? 'Reserva de 5 min' : 'Guardado por 7 días',
                'expira_en_iso' => $expiraLineaCarbon->toIso8601String(),
                'max_disponible' => $maxDisponible,
                'imagen' => $producto->imagen,
            ];

            $items[] = $itemData;

            if ($esPiezaUnica) {
                $itemsPiezasUnicas[] = $itemData;
                $subtotalPiezasUnicas += $lineaSubtotal;
                $cantidadPiezasUnicas += $cantidadInt;
            } else {
                $itemsRegulares[] = $itemData;
                $subtotalRegulares += $lineaSubtotal;
                $cantidadRegulares += $cantidadInt;
            }
        }

        $tienePiezasUnicas = ! empty($itemsPiezasUnicas);
        $tieneProductosRegulares = ! empty($itemsRegulares);

        if ($tienePiezasUnicas) {
            if (! $expiraUnicaCarbon instanceof Carbon) {
                $expiraEnIso = session('carrito_expira_en');
                $expiraUnicaCarbon = is_string($expiraEnIso)
                    ? Carbon::parse($expiraEnIso)
                    : now()->addMinutes(self::MINUTOS_EXPIRACION_RESERVA);
            }

            $segundosRestantes = max(0, (int) now()->diffInSeconds($expiraUnicaCarbon, false));
            $minutos = intdiv($segundosRestantes, 60);
            $segundos = $segundosRestantes % 60;
            $tiempoFormateado = sprintf('%02d:%02d min', $minutos, $segundos);
            $expiraIsoFinal = $expiraUnicaCarbon->toIso8601String();
            $expiraMsFinal = $expiraUnicaCarbon->getTimestamp() * 1000;
        } else {
            // Solo hay productos comunes: no aplica temporizador de 5 minutos
            $segundosRestantes = 0;
            $tiempoFormateado = '05:00 min';
            $expiraIsoFinal = null;
            $expiraMsFinal = 0;
        }

        return [
            'items' => $items,
            'items_piezas_unicas' => $itemsPiezasUnicas,
            'items_regulares' => $itemsRegulares,
            'tiene_piezas_unicas' => $tienePiezasUnicas,
            'tiene_productos_regulares' => $tieneProductosRegulares,
            'tiene_ambos_tipos' => $tienePiezasUnicas && $tieneProductosRegulares,
            'cantidad_piezas_unicas' => $cantidadPiezasUnicas,
            'cantidad_regulares' => $cantidadRegulares,
            'subtotal_piezas_unicas' => $subtotalPiezasUnicas,
            'subtotal_regulares' => $subtotalRegulares,
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'cantidad_total' => $cantidadTotal,
            'expira_en_iso' => $expiraIsoFinal,
            'expira_en_ms' => $expiraMsFinal,
            'segundos_restantes' => $segundosRestantes,
            'tiempo_formateado' => $tiempoFormateado,
            'vigencia_regulares_dias' => self::DIAS_VIGENCIA_PRODUCTO_COMUN,
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
     * Obtiene la promoción activa o preventa programada asociada a un producto (si existe).
     */
    public static function obtenerPromocionDeProducto(?Producto $producto): ?Promocion
    {
        if ($producto === null || ! Schema::hasTable('promociones') || ! Schema::hasColumn('promociones', 'producto_id')) {
            return null;
        }

        $promociones = $producto->relationLoaded('promociones')
            ? $producto->promociones
            : Promocion::where('producto_id', $producto->id)->where('activa', true)->get();

        /** @var Promocion|null $promo */
        $promo = $promociones->first(fn (Promocion $p): bool => in_array($p->faseActual(), ['preventa_exclusiva', 'abierta_publico', 'programada'], true));

        return $promo;
    }

    /**
     * Obtiene el registro de PiezaUnica asociado a una variante SKU (si se trata de un ejemplar único 1/1).
     */
    public static function obtenerPiezaUnicaDeVariante(int $varianteSkuId): ?PiezaUnica
    {
        if (! Schema::hasTable('piezas_unicas') || ! Schema::hasTable('existencias')) {
            return null;
        }

        return PiezaUnica::with('existencia.sucursal')
            ->whereHas('existencia', fn ($q) => $q->where('variante_sku_id', $varianteSkuId))
            ->first();
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
        if (! auth()->check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'unauthenticated',
                    'message' => 'Inicia sesión o regístrate para agregar productos al carrito.',
                    'login_url' => route('login'),
                    'redirect' => route('login'),
                ], 401);
            }

            session(['url.intended' => url()->previous() ?: route('public.home')]);

            return redirect()->route('login')
                ->with('status', 'Inicia sesión o regístrate para agregar productos al carrito.')
                ->with('auth_notice', 'Inicia sesión o regístrate para agregar productos al carrito.');
        }

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

        // 0. Validar que la pieza no esté dada de baja del catálogo
        if ($producto && ! $producto->activo) {
            $mensajeInactivo = "La pieza «{$nombrePieza}» ha sido dada de baja del catálogo y no se encuentra disponible para reserva.";

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $mensajeInactivo,
                ], 422);
            }

            return back()
                ->with('error', $mensajeInactivo)
                ->withErrors(['stock' => $mensajeInactivo]);
        }

        // 0.B Validar Fase de Preventa Exclusiva para Clientes Frecuentes (5+ compras) vs Apertura al Público General
        if ($producto) {
            $promoProducto = self::obtenerPromocionDeProducto($producto);
            $usuarioActual = $request->user();
            $clienteActual = $usuarioActual && Schema::hasTable('clientes')
                ? Cliente::with(['pedidos', 'clienteFrecuente'])->where('usuario_id', $usuarioActual->id)->first()
                : null;

            if ($promoProducto !== null) {
                $fasePromo = $promoProducto->faseActual();

                if ($fasePromo === 'programada') {
                    $fechaInicioStr = $promoProducto->fechaInicioPreventa()->format('d/m/Y H:i');
                    $mensajeProgramada = "Lanzamiento Programado: La preventa de «{$nombrePieza}» aún no ha iniciado (inicia el {$fechaInicioStr}).";

                    if ($request->expectsJson()) {
                        return response()->json([
                            'status' => 'error',
                            'message' => $mensajeProgramada,
                        ], 422);
                    }

                    return back()
                        ->with('error', $mensajeProgramada)
                        ->withErrors(['stock' => $mensajeProgramada]);
                }

                if ($fasePromo === 'preventa_exclusiva' && ! $promoProducto->puedeAdquirir($usuarioActual, $clienteActual)) {
                    $comprasActuales = $clienteActual?->totalComprasRealizadas() ?? 0;
                    $fechaAperturaStr = $promoProducto->fechaAperturaPublica()->format('d/m/Y H:i');
                    $mensajePreventaExclusiva = "Fase de Preventa Exclusiva VIP: «{$nombrePieza}» se encuentra en acceso anticipado exclusivo para Clientes Frecuentes (mínimo 5 compras completadas; compras actuales: {$comprasActuales}). La promoción se desbloqueará para el público general el {$fechaAperturaStr}.";

                    if ($request->expectsJson()) {
                        return response()->json([
                            'status' => 'error',
                            'message' => $mensajePreventaExclusiva,
                        ], 422);
                    }

                    return back()
                        ->with('error', $mensajePreventaExclusiva)
                        ->withErrors(['stock' => $mensajePreventaExclusiva]);
                }
            } elseif ($producto->preventa_exclusiva_hasta instanceof Carbon && now()->lt($producto->preventa_exclusiva_hasta)) {
                $esAutorizado = ($usuarioActual && method_exists($usuarioActual, 'esAdmin') && $usuarioActual->esAdmin())
                    || ($clienteActual !== null && $clienteActual->esClienteFrecuente());

                if (! $esAutorizado) {
                    $comprasActuales = $clienteActual?->totalComprasRealizadas() ?? 0;
                    $fechaAperturaStr = $producto->preventa_exclusiva_hasta->format('d/m/Y H:i');
                    $mensajePreventaExclusiva = "Fase de Preventa Exclusiva VIP: «{$nombrePieza}» se encuentra en acceso anticipado exclusivo para Clientes Frecuentes (mínimo 5 compras completadas; compras actuales: {$comprasActuales}). Se abrirá al público general el {$fechaAperturaStr}.";

                    if ($request->expectsJson()) {
                        return response()->json([
                            'status' => 'error',
                            'message' => $mensajePreventaExclusiva,
                        ], 422);
                    }

                    return back()
                        ->with('error', $mensajePreventaExclusiva)
                        ->withErrors(['stock' => $mensajePreventaExclusiva]);
                }
            }
        }

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

        // 2. Validar exclusividad de Pieza Única (1/1) y bloqueo de concurrencia (5 min)
        $piezaUnica = self::obtenerPiezaUnicaDeVariante($varianteId);
        $stockLibreEnBoveda = (int) $variante->existencias->sum('cantidad');

        if ($piezaUnica !== null) {
            if ($piezaUnica->estado === 'reservada') {
                $mensajeBloqueo = "Bloqueo de concurrencia: La pieza única «{$nombrePieza}» (Serie: {$piezaUnica->numero_serie}) se encuentra reservada temporalmente en bóveda por otro cliente (bloqueo activo de 5 minutos) y no está disponible en bóveda.";

                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $mensajeBloqueo,
                    ], 422);
                }

                return back()
                    ->with('error', $mensajeBloqueo)
                    ->withErrors(['stock' => $mensajeBloqueo]);
            }

            if ($piezaUnica->estado === 'vendida' || $stockLibreEnBoveda <= 0) {
                $mensajeVendida = "Operación bloqueada: La pieza única «{$nombrePieza}» (Serie: {$piezaUnica->numero_serie}) se encuentra AGOTADO y no se encuentra disponible en bóveda.";

                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $mensajeVendida,
                    ], 422);
                }

                return back()
                    ->with('error', $mensajeVendida)
                    ->withErrors(['stock' => $mensajeVendida]);
            }

            if (($cantidadActualEnCarrito + $cantidad) > 1 && $stockLibreEnBoveda <= 1) {
                $mensajeUnica = "Exclusividad de Pieza Única: El ejemplar «{$nombrePieza}» (Serie: {$piezaUnica->numero_serie}) es una edición única (1/1) y no permite adquirir múltiples unidades.";

                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $mensajeUnica,
                    ], 422);
                }

                return back()
                    ->with('error', $mensajeUnica)
                    ->withErrors(['stock' => $mensajeUnica]);
            }
        }

        // 3. Validar stock disponible en bóveda para inventario general / híbrido
        // Como cada reserva activa ya descontó de `existencias`, la suma actual en `existencias`
        // representa las unidades libres adicionales que aún quedan en bóveda.
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

        $esPiezaUnicaVariante = self::esVariantePiezaUnica($variante, $cantidadActualEnCarrito);
        $expiraEn = $esPiezaUnicaVariante
            ? now()->addMinutes(self::MINUTOS_EXPIRACION_RESERVA)
            : now()->addDays(self::DIAS_VIGENCIA_PRODUCTO_COMUN);

        /** @var list<int> $idsReservasSesion */
        $idsReservasSesion = array_values(array_map('intval', (array) session('carrito_reservas_ids', [])));

        // 3. Disminución inmediata del stock en base de datos y creación de ReservaStock
        //    (5 minutos para Piezas Únicas; 7 días para Productos Comunes / Regulares)
        DB::transaction(function () use ($varianteId, $cantidad, $expiraEn, $esPiezaUnicaVariante, &$idsReservasSesion): void {
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

            // Si se agregó una Pieza Única, actualizar únicamente las reservas de 5 minutos (sin alterar las de 7 días)
            if ($esPiezaUnicaVariante && ! empty($idsReservasSesion)) {
                ReservaStock::whereIn('id', $idsReservasSesion)
                    ->where('estado', 'activa')
                    ->where('expira_en', '<=', now()->addMinutes(15))
                    ->update(['expira_en' => $expiraEn]);
            }
        });

        $carrito[$varianteId] = $cantidadActualEnCarrito + $cantidad;

        // Congelar el precio unitario vigente EXCLUSIVAMENTE para Productos Comunes (no aplica a Piezas Únicas).
        // Si el cliente ya tenía este producto común en el carrito y agrega más unidades, se conserva el precio congelado original.
        /** @var array<int, float> $preciosCongelados */
        $preciosCongelados = (array) session('carrito_precios_congelados', []);
        if ($esPiezaUnicaVariante) {
            unset($preciosCongelados[$varianteId]);
        } elseif (! isset($preciosCongelados[$varianteId]) || (float) $preciosCongelados[$varianteId] <= 0) {
            $preciosCongelados[$varianteId] = (float) ($variante->precio ?? $producto?->precio ?? 0.0);
        }

        $datosSesion = [
            'carrito_archivum' => $carrito,
            'carrito_precios_congelados' => $preciosCongelados,
            'carrito_reservas_ids' => array_values(array_unique($idsReservasSesion)),
        ];

        if ($esPiezaUnicaVariante) {
            $datosSesion['carrito_expira_en'] = $expiraEn->toIso8601String();
        }

        session($datosSesion);

        $this->sincronizarCarritoBaseDatos($carrito);

        $mensajeExito = $esPiezaUnicaVariante
            ? "«{$nombrePieza}» reservada temporalmente por 5 minutos en tu Carrito de Compras."
            : "«{$nombrePieza}» agregada a tu Carrito de Compras (guardada por 7 días).";

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => $mensajeExito,
                'carrito' => self::obtenerEstadoCarrito(),
            ]);
        }

        return back()
            ->with('status', $mensajeExito)
            ->with('abrir_carrito', true);
    }

    /**
     * Actualiza la cantidad de unidades seleccionadas de un artículo en el carrito,
     * recalculando y ajustando de forma transaccional el stock en `existencias` y en `reservas_stock`.
     */
    public function actualizarCantidad(Request $request, int $varianteSkuId): JsonResponse|RedirectResponse
    {
        if (! auth()->check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'unauthenticated',
                    'message' => 'Inicia sesión o regístrate para agregar productos al carrito.',
                    'login_url' => route('login'),
                    'redirect' => route('login'),
                ], 401);
            }

            session(['url.intended' => url()->previous() ?: route('public.home')]);

            return redirect()->route('login')
                ->with('status', 'Inicia sesión o regístrate para agregar productos al carrito.')
                ->with('auth_notice', 'Inicia sesión o regístrate para agregar productos al carrito.');
        }

        self::liberarReservasExpiradas();

        $validated = $request->validate([
            'cantidad' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $nuevaCantidad = (int) $validated['cantidad'];

        /** @var array<int, int> $carrito */
        $carrito = session('carrito_archivum', []);

        if (! isset($carrito[$varianteSkuId])) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El artículo no se encuentra en tu Carrito de Compras.',
                ], 404);
            }

            return back()->with('error', 'El artículo no se encuentra en el carrito.');
        }

        $cantidadActual = (int) $carrito[$varianteSkuId];

        if ($nuevaCantidad === $cantidadActual) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Cantidad sin cambios.',
                    'carrito' => self::obtenerEstadoCarrito(),
                ]);
            }

            return back();
        }

        $variante = VarianteSku::with(['producto.variantesSku', 'existencias'])->findOrFail($varianteSkuId);
        $producto = $variante->producto;
        $nombrePieza = $producto?->nombre ?? 'Pieza seleccionada';

        // Validar exclusividad de Pieza Única (1/1)
        $piezaUnica = self::obtenerPiezaUnicaDeVariante($varianteSkuId);
        if ($piezaUnica !== null && $nuevaCantidad > 1) {
            $mensajeUnica = "Exclusividad de Pieza Única: El ejemplar «{$nombrePieza}» (Serie: {$piezaUnica->numero_serie}) es una edición única (1/1) y no permite seleccionar múltiples unidades.";
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $mensajeUnica,
                ], 422);
            }

            return back()->with('error', $mensajeUnica);
        }

        $diferencia = $nuevaCantidad - $cantidadActual;
        /** @var list<int> $idsReservasSesion */
        $idsReservasSesion = array_values(array_map('intval', (array) session('carrito_reservas_ids', [])));
        $esPiezaUnicaVariante = self::esVariantePiezaUnica($variante, $cantidadActual);
        $expiraEn = $esPiezaUnicaVariante
            ? (is_string(session('carrito_expira_en'))
                ? Carbon::parse((string) session('carrito_expira_en'))
                : now()->addMinutes(self::MINUTOS_EXPIRACION_RESERVA))
            : now()->addDays(self::DIAS_VIGENCIA_PRODUCTO_COMUN);

        if ($diferencia > 0) {
            // Validar cupo de preventa si aplica
            if ($producto && self::esPiezaPreventa($producto, $variante)) {
                try {
                    app(PreventaService::class)->validarCupoPreventa($producto->id, $nuevaCantidad);
                } catch (RuntimeException $e) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'status' => 'error',
                            'message' => $e->getMessage(),
                        ], 422);
                    }

                    return back()->with('error', $e->getMessage());
                }
            }

            $stockLibreEnBoveda = (int) $variante->existencias->sum('cantidad');
            if ($stockLibreEnBoveda < $diferencia) {
                $mensajeInsuficiente = "No hay suficientes ejemplares de «{$nombrePieza}» disponibles en bóveda (disponibles adicionales: {$stockLibreEnBoveda}).";
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $mensajeInsuficiente,
                    ], 422);
                }

                return back()->with('error', $mensajeInsuficiente);
            }

            DB::transaction(function () use ($varianteSkuId, $diferencia, $expiraEn, &$idsReservasSesion): void {
                $carritoBd = $this->obtenerOCrearCarritoBaseDatos();
                $porDescontar = $diferencia;

                $existencias = Existencia::where('variante_sku_id', $varianteSkuId)
                    ->where('cantidad', '>', 0)
                    ->orderByDesc('cantidad')
                    ->lockForUpdate()
                    ->get();

                $totalBloqueado = (int) $existencias->sum('cantidad');
                if ($totalBloqueado < $diferencia) {
                    throw new RuntimeException('No hay suficiente stock en bóveda para incrementar la cantidad.');
                }

                foreach ($existencias as $existencia) {
                    if ($porDescontar <= 0) {
                        break;
                    }

                    $descuentoSucursal = min((int) $existencia->cantidad, $porDescontar);
                    $existencia->decrement('cantidad', $descuentoSucursal);
                    $porDescontar -= $descuentoSucursal;

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
            });
        } else {
            // Liberar unidades de vuelta a existencias
            $unidadesALiberar = abs($diferencia);

            DB::transaction(function () use ($varianteSkuId, $unidadesALiberar, &$idsReservasSesion): void {
                $carritoBd = $this->obtenerOCrearCarritoBaseDatos();
                $porLiberar = $unidadesALiberar;

                $reservasActivas = ReservaStock::with('existencia')
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
                    ->orderByDesc('id')
                    ->lockForUpdate()
                    ->get();

                $idsRemover = [];

                foreach ($reservasActivas as $reserva) {
                    if ($porLiberar <= 0) {
                        break;
                    }

                    $liberarDeEsta = min((int) $reserva->cantidad, $porLiberar);
                    $existencia = Existencia::lockForUpdate()->find($reserva->existencia_id);
                    if ($existencia) {
                        $existencia->increment('cantidad', $liberarDeEsta);
                    }

                    if ($liberarDeEsta >= (int) $reserva->cantidad) {
                        $reserva->update(['estado' => 'liberada']);
                        $idsRemover[] = (int) $reserva->id;
                    } else {
                        $reserva->decrement('cantidad', $liberarDeEsta);
                    }

                    $porLiberar -= $liberarDeEsta;
                }

                if (! empty($idsRemover)) {
                    $idsReservasSesion = array_values(array_diff($idsReservasSesion, $idsRemover));
                }
            });
        }

        $carrito[$varianteSkuId] = $nuevaCantidad;
        session([
            'carrito_archivum' => $carrito,
            'carrito_reservas_ids' => array_values(array_unique($idsReservasSesion)),
        ]);

        $this->sincronizarCarritoBaseDatos($carrito);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Cantidad de «{$nombrePieza}» actualizada a {$nuevaCantidad} unidades.",
                'carrito' => self::obtenerEstadoCarrito(),
            ]);
        }

        return back()->with('status', "Cantidad de «{$nombrePieza}» actualizada a {$nuevaCantidad}.");
    }

    /**
     * Elimina un artículo del carrito de adquisiciones y libera de inmediato
     * su stock reservado de vuelta a la tabla `existencias`.
     */
    public function remover(Request $request, int $varianteSkuId): RedirectResponse|JsonResponse
    {
        if (! auth()->check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'unauthenticated',
                    'message' => 'Inicia sesión o regístrate para agregar productos al carrito.',
                    'login_url' => route('login'),
                    'redirect' => route('login'),
                ], 401);
            }

            session(['url.intended' => url()->previous() ?: route('public.home')]);

            return redirect()->route('login')
                ->with('status', 'Inicia sesión o regístrate para agregar productos al carrito.')
                ->with('auth_notice', 'Inicia sesión o regístrate para agregar productos al carrito.');
        }

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
        /** @var array<int, float> $preciosCongelados */
        $preciosCongelados = (array) session('carrito_precios_congelados', []);
        unset($preciosCongelados[$varianteSkuId]);

        session([
            'carrito_archivum' => $carrito,
            'carrito_precios_congelados' => $preciosCongelados,
            'carrito_reservas_ids' => $idsReservasSesion,
        ]);

        if (empty($carrito)) {
            session()->forget(['carrito_expira_en', 'carrito_reservas_ids', 'carrito_precios_congelados']);
        }

        $this->sincronizarCarritoBaseDatos($carrito);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'carrito' => self::obtenerEstadoCarrito(),
            ]);
        }

        return back()
            ->with('status', 'Producto eliminado de tu Carrito de Compras y stock restaurado en el catálogo.')
            ->with('abrir_carrito', true);
    }

    /**
     * Libera de forma inmediata únicamente las reservas de Piezas Únicas (5 minutos)
     * cuando su temporizador llega a cero, conservando intactos los Productos Comunes
     * que tienen vigencia de 7 días en el carrito.
     */
    public function expirarReservas(Request $request): RedirectResponse|JsonResponse
    {
        /** @var list<int> $idsReservasSesion */
        $idsReservasSesion = array_values(array_map('intval', (array) session('carrito_reservas_ids', [])));
        /** @var array<int, int> $carritoActual */
        $carritoActual = (array) session('carrito_archivum', []);

        $variantesUnicasIds = [];
        if (! empty($carritoActual)) {
            $variantesEnCarrito = VarianteSku::with(['producto.coleccionable', 'existencias'])
                ->whereIn('id', array_map('intval', array_keys($carritoActual)))
                ->get();

            foreach ($variantesEnCarrito as $varItem) {
                $cant = (int) ($carritoActual[$varItem->id] ?? 0);
                if (self::esVariantePiezaUnica($varItem, $cant)) {
                    $variantesUnicasIds[] = (int) $varItem->id;
                }
            }
        }

        $idsReservasExpiradas = [];

        DB::transaction(function () use ($idsReservasSesion, $variantesUnicasIds, &$idsReservasExpiradas): void {
            $carritoBd = $this->obtenerOCrearCarritoBaseDatos();

            if (! empty($idsReservasSesion) || $carritoBd) {
                $reservas = ReservaStock::with('existencia')
                    ->where('estado', 'activa')
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
                    $existencia = $reserva->existencia ?? Existencia::lockForUpdate()->find($reserva->existencia_id);
                    $varId = (int) ($existencia?->variante_sku_id ?? 0);

                    // Expirar únicamente si es una Pieza Única (5 min) o si su fecha de expiración ya venció
                    $esReservaDePiezaUnica = in_array($varId, $variantesUnicasIds, true)
                        || ($reserva->expira_en instanceof Carbon && $reserva->expira_en->lte(now()->addMinutes(15)));
                    $yaVencio = $reserva->expira_en instanceof Carbon && $reserva->expira_en->lte(now());

                    if (! $esReservaDePiezaUnica && ! $yaVencio) {
                        continue;
                    }

                    if ($existencia) {
                        $existenciaLocked = Existencia::lockForUpdate()->find($existencia->id);
                        if ($existenciaLocked) {
                            $existenciaLocked->increment('cantidad', $reserva->cantidad);
                        }

                        $piezaUnica = PiezaUnica::where('existencia_id', $existencia->id)
                            ->where('estado', 'reservada')
                            ->lockForUpdate()
                            ->first();

                        if ($piezaUnica) {
                            $piezaUnica->update(['estado' => 'disponible']);
                        }
                    }

                    $reserva->update(['estado' => 'expirada']);
                    $idsReservasExpiradas[] = (int) $reserva->id;
                }
            }
        });

        // Retirar del carrito en sesión únicamente las Piezas Únicas expiradas, conservando los Productos Comunes (7 días)
        foreach ($variantesUnicasIds as $varUnicaId) {
            unset($carritoActual[$varUnicaId]);
        }

        $idsReservasRestantes = array_values(array_diff($idsReservasSesion, $idsReservasExpiradas));
        session()->forget('carrito_expira_en');

        if (empty($carritoActual)) {
            session()->forget(['carrito_archivum', 'carrito_reservas_ids']);
            $this->sincronizarCarritoBaseDatos([]);
        } else {
            session([
                'carrito_archivum' => $carritoActual,
                'carrito_reservas_ids' => $idsReservasRestantes,
            ]);
            $this->sincronizarCarritoBaseDatos($carritoActual);
        }

        self::liberarReservasExpiradas();

        $mensaje = empty($carritoActual)
            ? 'El temporizador de 5 minutos ha expirado por inactividad. Las piezas únicas fueron liberadas y devueltas al catálogo.'
            : 'El temporizador de 5 minutos para tus Piezas Únicas ha expirado y regresaron al catálogo. Tus Productos Regulares continúan guardados por 7 días.';

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
     * Si el cliente tiene artículos en custodia pero no ha iniciado sesión,
     * se intercepta el acceso y se redirige a autenticación para proteger el lote.
     */
    public function checkout(): View|RedirectResponse
    {
        $estadoCarrito = self::obtenerEstadoCarrito();

        if (! auth()->check()) {
            if (! empty($estadoCarrito['items'])) {
                session(['url.intended' => route('checkout.index')]);

                return redirect()->route('public.home', ['login' => '1', 'intended' => 'checkout'])
                    ->with('warning', 'Identificación requerida: Inicia sesión o crea tu cuenta para formalizar la adquisición notariada de tus piezas reservadas.');
            }

            return view('public.checkout', [
                'carrito' => $estadoCarrito,
                'usuario' => null,
            ]);
        }

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
        if (! auth()->check()) {
            session(['url.intended' => route('checkout.index')]);

            return redirect()->route('public.home', ['login' => '1', 'intended' => 'checkout'])
                ->with('warning', 'Debes iniciar sesión para procesar la adquisición.');
        }

        $teniaItemsEnSesion = ! empty(session('carrito_archivum', []));
        $estadoCarrito = self::obtenerEstadoCarrito();

        if (empty($estadoCarrito['items'])) {
            if ($teniaItemsEnSesion) {
                $mensajeExpirado = 'Transacción bloqueada: Tu reserva temporal de 5 minutos ha expirado por inactividad y el stock fue devuelto al catálogo.';

                return back()
                    ->withInput()
                    ->with('error', $mensajeExpirado)
                    ->withErrors(['stock' => $mensajeExpirado]);
            }

            return redirect()->route('public.home')->with('warning', 'Tu Carrito de Compras está vacío. Selecciona al menos un producto antes de continuar.');
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

                        PiezaUnica::where('existencia_id', $reserva->existencia_id)
                            ->whereIn('estado', ['reservada', 'disponible'])
                            ->update(['estado' => 'vendida']);

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

                        PiezaUnica::where('existencia_id', $existencia->id)
                            ->whereIn('estado', ['reservada', 'disponible'])
                            ->update(['estado' => 'vendida']);
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

        // Evaluar si con esta compra el cliente alcanzó las 5 compras para convertirse en Cliente Frecuente VIP
        if ($pedido->cliente) {
            app(PromocionDescuentoService::class)->sincronizarMembresiaPorCompras($pedido->cliente);
        }

        // 7. Limpiar el carrito y las referencias de reserva temporal en la sesión del cliente
        session()->forget(['carrito_archivum', 'carrito_precios_congelados', 'carrito_reservas_ids', 'carrito_expira_en']);
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
     * Panel del cliente: historial de adquisiciones, seguimiento interactivo de envíos
     * y notificaciones de Preventas Exclusivas para Clientes Frecuentes (5+ compras).
     */
    public function misPedidos(): View
    {
        $pedidos = collect();
        $clienteActual = null;

        if (auth()->check() && Schema::hasTable('clientes')) {
            $clienteActual = Cliente::with(['pedidos', 'clienteFrecuente'])->where('usuario_id', auth()->id())->first();
        }

        if (Schema::hasTable('pedidos')) {
            $idsSesion = session('pedidos_recientes', []);
            $clienteId = $clienteActual?->id;

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

        $esClienteFrecuente = $clienteActual?->esClienteFrecuente() ?? false;
        $totalComprasCliente = $clienteActual?->totalComprasRealizadas() ?? 0;
        $comprasRestantesFrecuente = $clienteActual
            ? $clienteActual->comprasRestantesParaFrecuente()
            : Cliente::COMPRAS_MINIMAS_FRECUENTE;

        if ($clienteActual !== null && $esClienteFrecuente) {
            app(PromocionDescuentoService::class)->sincronizarMembresiaPorCompras($clienteActual);
        }

        $promocionesPreventa = collect();
        if (Schema::hasTable('promociones')) {
            $queryPromos = Promocion::query()->where('activa', true);
            if (Schema::hasColumn('promociones', 'producto_id')) {
                $queryPromos->with([
                    'producto.catalogo',
                    'producto.libro',
                    'producto.disco',
                    'producto.coleccionable',
                    'producto.variantesSku.existencias',
                ]);
            }
            $promocionesPreventa = $queryPromos
                ->orderByDesc('es_preventa_exclusiva')
                ->orderBy('inicia_en')
                ->get()
                ->filter(fn (Promocion $p): bool => in_array($p->faseActual(), ['preventa_exclusiva', 'abierta_publico', 'programada'], true))
                ->values();
        }

        return view('public.mis-pedidos', [
            'pedidos' => $pedidos,
            'etapasRastreo' => self::ETAPAS_RASTREO,
            'motivosDevolucion' => Devolucion::MOTIVOS_CATALOGO,
            'clienteActual' => $clienteActual,
            'esClienteFrecuente' => $esClienteFrecuente,
            'totalComprasCliente' => $totalComprasCliente,
            'comprasRestantesFrecuente' => $comprasRestantesFrecuente,
            'promocionesPreventa' => $promocionesPreventa,
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
            'evidencias.*' => ['file', 'mimes:jpg,jpeg,png,webp,gif,pdf', 'max:5120'],
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
     * Sirve los archivos públicos almacenados en `storage/app/public` (como las fotografías
     * de evidencia de devoluciones) garantizando su visualización sin errores 404.
     */
    public function servirArchivoPublico(string $path): BinaryFileResponse|StreamedResponse
    {
        $rutaLimpia = ltrim(str_replace(['..', '\\'], ['', '/'], $path), '/');

        if (! Storage::disk('public')->exists($rutaLimpia)) {
            abort(404);
        }

        return Storage::disk('public')->response($rutaLimpia);
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

        /** @var array<int, float> $preciosCongelados */
        $preciosCongelados = (array) session('carrito_precios_congelados', []);
        $tieneColumnaPrecioCongelado = Schema::hasTable('carrito_items')
            && Schema::hasColumn('carrito_items', 'precio_unitario_congelado');

        // Conservar cualquier precio congelado que ya existiera en BD para productos comunes vigentes
        if ($tieneColumnaPrecioCongelado) {
            $existentesBd = CarritoItem::where('carrito_id', $carritoBd->id)->get();
            foreach ($existentesBd as $itemExistente) {
                $varIdExistente = (int) $itemExistente->variante_sku_id;
                if ($itemExistente->precio_unitario_congelado !== null && ! isset($preciosCongelados[$varIdExistente])) {
                    $preciosCongelados[$varIdExistente] = (float) $itemExistente->precio_unitario_congelado;
                }
            }
        }

        CarritoItem::where('carrito_id', $carritoBd->id)->delete();

        if (empty($carritoSesion)) {
            return;
        }

        $variantesMap = VarianteSku::with(['producto.coleccionable', 'existencias'])
            ->whereIn('id', array_map('intval', array_keys($carritoSesion)))
            ->get()
            ->keyBy('id');

        foreach ($carritoSesion as $varianteSkuId => $cantidad) {
            $varId = (int) $varianteSkuId;
            $cantInt = (int) $cantidad;
            /** @var VarianteSku|null $variante */
            $variante = $variantesMap->get($varId);
            $esPiezaUnica = $variante !== null && self::esVariantePiezaUnica($variante, $cantInt);

            $precioCongelado = null;
            if (! $esPiezaUnica) {
                $precioCongelado = isset($preciosCongelados[$varId]) && (float) $preciosCongelados[$varId] > 0
                    ? (float) $preciosCongelados[$varId]
                    : (float) ($variante?->precio ?? $variante?->producto?->precio ?? 0.0);
                $preciosCongelados[$varId] = $precioCongelado;
            } else {
                unset($preciosCongelados[$varId]);
            }

            $atributosItem = [
                'carrito_id' => $carritoBd->id,
                'variante_sku_id' => $varId,
                'cantidad' => $cantInt,
            ];

            if ($tieneColumnaPrecioCongelado) {
                $atributosItem['precio_unitario_congelado'] = $precioCongelado;
            }

            CarritoItem::create($atributosItem);
        }

        session(['carrito_precios_congelados' => $preciosCongelados]);
    }

    /**
     * Vincula el carrito en sesión y sus reservas activas al perfil de Cliente
     * y su carrito en base de datos al momento de iniciar sesión o registrarse.
     */
    public static function sincronizarCarritoTrasLogin(Usuario $user): void
    {
        if (! $user->esCliente() || ! Schema::hasTable('clientes') || ! Schema::hasTable('carritos')) {
            return;
        }

        $cliente = Cliente::where('usuario_id', $user->id)->first();
        if (! $cliente) {
            $cliente = Cliente::create([
                'usuario_id' => $user->id,
                'nombre' => $user->nombre,
                'apellidos' => 'Coleccionista',
                'email' => $user->email,
            ]);
        }

        $carritoBd = Carrito::firstOrCreate(['cliente_id' => $cliente->id]);

        /** @var array<int, int> $carritoSesion */
        $carritoSesion = session('carrito_archivum', []);
        /** @var array<int, float> $preciosCongelados */
        $preciosCongelados = (array) session('carrito_precios_congelados', []);
        $tieneColumnaPrecioCongelado = Schema::hasTable('carrito_items')
            && Schema::hasColumn('carrito_items', 'precio_unitario_congelado');

        /** @var list<int> $idsReservasSesion */
        $idsReservasSesion = array_values(array_map('intval', (array) session('carrito_reservas_ids', [])));

        // 1. Si hay reservas activas en la sesión del invitado, asociarlas al carrito_id en BD
        if (! empty($idsReservasSesion) && Schema::hasTable('reservas_stock')) {
            ReservaStock::whereIn('id', $idsReservasSesion)
                ->where('estado', 'activa')
                ->whereNull('carrito_id')
                ->update(['carrito_id' => $carritoBd->id]);
        }

        // 2. Si la sesión tiene artículos, persistirlos en `carrito_items`
        if (! empty($carritoSesion) && Schema::hasTable('carrito_items')) {
            CarritoItem::where('carrito_id', $carritoBd->id)->delete();

            $variantesMap = VarianteSku::with(['producto.coleccionable', 'existencias'])
                ->whereIn('id', array_map('intval', array_keys($carritoSesion)))
                ->get()
                ->keyBy('id');

            foreach ($carritoSesion as $varianteSkuId => $cantidad) {
                $varId = (int) $varianteSkuId;
                $cantInt = (int) $cantidad;
                /** @var VarianteSku|null $variante */
                $variante = $variantesMap->get($varId);
                $esPiezaUnica = $variante !== null && self::esVariantePiezaUnica($variante, $cantInt);

                $precioCongelado = null;
                if (! $esPiezaUnica) {
                    $precioCongelado = isset($preciosCongelados[$varId]) && (float) $preciosCongelados[$varId] > 0
                        ? (float) $preciosCongelados[$varId]
                        : (float) ($variante?->precio ?? $variante?->producto?->precio ?? 0.0);
                    $preciosCongelados[$varId] = $precioCongelado;
                }

                $atributosItem = [
                    'carrito_id' => $carritoBd->id,
                    'variante_sku_id' => $varId,
                    'cantidad' => $cantInt,
                ];

                if ($tieneColumnaPrecioCongelado) {
                    $atributosItem['precio_unitario_congelado'] = $precioCongelado;
                }

                CarritoItem::create($atributosItem);
            }
            session(['carrito_precios_congelados' => $preciosCongelados]);
        } elseif (empty($carritoSesion) && Schema::hasTable('carrito_items')) {
            // Si la sesión no tenía nada pero la BD sí, cargar desde la BD a la sesión (incluyendo precios congelados)
            $itemsBd = CarritoItem::where('carrito_id', $carritoBd->id)->get();
            if ($itemsBd->isNotEmpty()) {
                $nuevoMapa = [];
                foreach ($itemsBd as $itemBd) {
                    $varId = (int) $itemBd->variante_sku_id;
                    $nuevoMapa[$varId] = (int) $itemBd->cantidad;
                    if ($itemBd->precio_unitario_congelado !== null) {
                        $preciosCongelados[$varId] = (float) $itemBd->precio_unitario_congelado;
                    }
                }
                session([
                    'carrito_archivum' => $nuevoMapa,
                    'carrito_precios_congelados' => $preciosCongelados,
                ]);
            }
        }
    }
}
