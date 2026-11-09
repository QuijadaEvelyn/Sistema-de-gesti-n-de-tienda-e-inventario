<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Envio;
use App\Models\Existencia;
use App\Models\MovimientoInventario;
use App\Models\Pedido;
use App\Models\PiezaUnica;
use App\Models\Producto;
use App\Models\ReservaStock;
use App\Models\Sucursal;
use App\Models\Usuario;
use App\Services\SucursalContextoService;
use Database\Seeders\CatalogoProductoSeeder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Punto de entrada del panel /admin (admin.dashboard).
     *
     * Renderiza el panel administrativo principal filtrando de manera estricta
     * todas las métricas (ventas, ingresos estimados, volumen de pedidos, piezas en custodia,
     * logística, certificados COA y bitácora de lotes/movimientos) según la sucursal activa
     * y el rango de temporalidad seleccionado (hoy, 7d, 30d, 2026).
     */
    public function index(Request $request, SucursalContextoService $contextoService): View
    {
        if (! app()->runningUnitTests() && Schema::hasTable('productos') && Producto::count() === 0) {
            app(CatalogoProductoSeeder::class)->run();
        }

        /** @var Usuario|null $usuario */
        $usuario = $request->user()?->loadMissing('roles');

        // Protección estricta en controlador: El Dashboard Financiero General es exclusivo para el rol Administrador
        if (! $usuario || ! $usuario->esAdmin()) {
            abort(403, 'Acceso denegado: El Dashboard Financiero General es exclusivo para el rol de Administrador.');
        }

        $sucursales = $contextoService->obtenerSucursalesActivas();
        $sucursalActiva = $contextoService->resolverSucursalAdmin($request, $usuario);
        $sucursalId = $sucursalActiva?->id;
        $puedeVerGlobal = $contextoService->puedeVerVistaGlobal($usuario);

        $periodo = $contextoService->resolverPeriodo($request);
        $fechaInicio = $periodo['inicio'];
        $fechaFin = $periodo['fin'];

        // 1. Consulta estricta de Existencias / Piezas en Custodia por Sucursal
        $existenciasQuery = Existencia::query()
            ->with(['varianteSku.producto.catalogo', 'varianteSku.producto.libro', 'varianteSku.producto.disco', 'varianteSku.producto.coleccionable', 'sucursal', 'piezasUnicas'])
            ->when($sucursalId !== null, fn (Builder $q) => $q->where('sucursal_id', $sucursalId));

        $existenciasSede = (clone $existenciasQuery)
            ->orderByDesc('cantidad')
            ->orderBy('id')
            ->get();

        $unidadesEnCustodia = (int) $existenciasSede->sum('cantidad');
        $totalSkusSede = $existenciasSede->count();
        $alertasBajoStock = $existenciasSede->filter(
            fn (Existencia $e): bool => $e->cantidad <= $e->stock_minimo
        )->count();

        $valoracionInventario = round((float) $existenciasSede->sum(function (Existencia $existencia): float {
            $precio = (float) ($existencia->varianteSku?->precio ?? $existencia->varianteSku?->producto?->precio ?? 0.0);

            return ((int) $existencia->cantidad) * $precio;
        }), 2);

        // 2. Consulta estricta de Pedidos, Ventas e Ingresos por Sucursal y Rango de Fecha
        $pedidosQuery = Pedido::query()
            ->with(['cliente.usuario', 'envios.sucursal', 'pagos', 'detalles.varianteSku.producto'])
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->when($sucursalId !== null, function (Builder $q) use ($sucursalId): void {
                $q->where(function (Builder $sub) use ($sucursalId): void {
                    $sub->whereHas('envios', fn (Builder $env) => $env->where('sucursal_id', $sucursalId))
                        ->orWhereHas('reservasStock.existencia', fn (Builder $ex) => $ex->where('sucursal_id', $sucursalId));
                });
            });

        $pedidosPeriodo = (clone $pedidosQuery)->latest('created_at')->get();
        $volumenPedidos = $pedidosPeriodo->count();

        $ventasConfirmadas = round((float) $pedidosPeriodo
            ->whereIn('estatus', ['pagado', 'confirmado', 'preparando', 'enviado', 'entregado'])
            ->sum('total'), 2);

        // Factor de proyección de ingresos estimados según el período seleccionado y la valoración de la sede
        $factorProyeccion = match ($periodo['codigo']) {
            'hoy' => 0.015,
            '7d' => 0.06,
            '2026' => 0.45,
            default => 0.14,
        };

        $ingresosEstimados = round($ventasConfirmadas + ($valoracionInventario * $factorProyeccion), 2);
        $ticketPromedio = $volumenPedidos > 0
            ? round($ventasConfirmadas / $volumenPedidos, 2)
            : round($totalSkusSede > 0 ? ($valoracionInventario / max(1, $unidadesEnCustodia)) : 0.0, 2);

        // 3. Logística Activa (Envíos, Reservas Temporales y Movimientos de Inventario en Sede y Período)
        $enviosActivosCount = Envio::query()
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->when($sucursalId !== null, fn (Builder $q) => $q->where('sucursal_id', $sucursalId))
            ->whereIn('estado_actual', ['en_preparacion', 'en_transito', 'en_camino'])
            ->count();

        $reservasActivasCount = (int) ReservaStock::query()
            ->where('estado', 'activa')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->when($sucursalId !== null, function (Builder $q) use ($sucursalId): void {
                $q->whereHas('existencia', fn (Builder $ex) => $ex->where('sucursal_id', $sucursalId));
            })
            ->sum('cantidad');

        $movimientosPeriodo = MovimientoInventario::query()
            ->with(['existencia.varianteSku.producto', 'existencia.sucursal', 'usuario'])
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->when($sucursalId !== null, function (Builder $q) use ($sucursalId): void {
                $q->whereHas('existencia', fn (Builder $ex) => $ex->where('sucursal_id', $sucursalId));
            })
            ->latest('created_at')
            ->limit(15)
            ->get();

        // 4. Lotes Certificados COA (PiezaUnica) en la Sucursal Seleccionada
        $piezasUnicasSede = PiezaUnica::query()
            ->with(['existencia.varianteSku.producto', 'existencia.sucursal'])
            ->when($sucursalId !== null, function (Builder $q) use ($sucursalId): void {
                $q->whereHas('existencia', fn (Builder $ex) => $ex->where('sucursal_id', $sucursalId));
            })
            ->get();

        $totalCertificadosCoa = $piezasUnicasSede->count();
        $coaDisponibles = $piezasUnicasSede->where('estado', 'disponible')->count();
        $coaReservadosVendidos = $piezasUnicasSede->whereIn('estado', ['reservada', 'vendida'])->count();

        return view('admin.dashboard', [
            'usuario' => $usuario,
            'sucursales' => $sucursales,
            'sucursalActiva' => $sucursalActiva,
            'puedeVerGlobal' => $puedeVerGlobal,
            'periodo' => $periodo,
            'metricas' => [
                'ventas_confirmadas' => $ventasConfirmadas,
                'ingresos_estimados' => $ingresosEstimados,
                'volumen_pedidos' => $volumenPedidos,
                'ticket_promedio' => $ticketPromedio,
                'unidades_en_custodia' => $unidadesEnCustodia,
                'total_skus' => $totalSkusSede,
                'alertas_bajo_stock' => $alertasBajoStock,
                'valoracion_inventario' => $valoracionInventario,
                'envios_activos' => $enviosActivosCount,
                'reservas_activas' => $reservasActivasCount,
                'movimientos_count' => $movimientosPeriodo->count(),
                'total_coa' => $totalCertificadosCoa,
                'coa_disponibles' => $coaDisponibles,
                'coa_comprometidos' => $coaReservadosVendidos,
            ],
            'existenciasSede' => $existenciasSede,
            'pedidosPeriodo' => $pedidosPeriodo,
            'movimientosPeriodo' => $movimientosPeriodo,
        ]);
    }

    /**
     * Actualiza de forma síncrona el contexto global de la sesión (sucursal activa y/o período temporal)
     * tanto desde el portal público como desde el panel administrativo.
     */
    public function cambiarContexto(Request $request, SucursalContextoService $contextoService): RedirectResponse
    {
        $usuario = $request->user();
        $esRutaAdmin = str_contains((string) $request->input('redirect_to', $request->headers->get('referer', '')), '/admin');

        if ($usuario && ($esRutaAdmin || $usuario->tieneRol('administrador', 'encargado', 'ventas', 'almacen', 'compras'))) {
            $sucursal = $contextoService->resolverSucursalAdmin($request, $usuario);
        } else {
            $sucursal = $contextoService->resolverSucursalPublica($request);
        }

        if ($request->filled('periodo')) {
            $contextoService->resolverPeriodo($request);
        }

        $nombreSede = $sucursal?->nombre ?? 'Todas las Sedes (Vista Global)';
        $redirectTo = $request->string('redirect_to')->trim()->toString();

        if ($redirectTo !== '' && str_starts_with($redirectTo, '/')) {
            return redirect($redirectTo)->with('status', "Contexto sincronizado: {$nombreSede}");
        }

        // Si la URL anterior (Referer) contenía un parámetro ?sucursal_id=... o ?sucursal=...,
        // actualizarlo con el nuevo identificador para evitar que la query string sobrescriba la sesión.
        $referer = (string) $request->headers->get('referer', '');
        if ($referer !== '') {
            $partesUrl = parse_url($referer);
            $path = $partesUrl['path'] ?? '';

            if ($path !== '' && str_starts_with($path, '/')) {
                $queryParams = [];
                if (! empty($partesUrl['query'])) {
                    parse_str($partesUrl['query'], $queryParams);
                }

                unset($queryParams['sucursal']);
                $queryParams['sucursal_id'] = $sucursal ? $sucursal->id : 'all';

                $destinoSincronizado = $path.'?'.http_build_query($queryParams);

                return redirect($destinoSincronizado)->with('status', "Contexto sincronizado: {$nombreSede}");
            }
        }

        return back()->with('status', "Contexto sincronizado: {$nombreSede}");
    }

    /**
     * Estado de la Terminal ARCHIVUM (indicador "TERMINAL ARCHIVO v4.8.2-PROD").
     * Muestra información técnica del sistema y de la sesión del operador.
     */
    public function terminal(Request $request, SucursalContextoService $contextoService): View
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();
        $sucursalActiva = $contextoService->resolverSucursalAdmin($request, $usuario);
        $sucursalId = $sucursalActiva?->id;

        return view('admin.terminal', [
            'version' => config('archivum.version', 'v4.8.2-PROD'),
            'usuario' => $usuario->loadMissing('roles'),
            'sistema' => [
                'PHP' => PHP_VERSION,
                'Laravel' => app()->version(),
                'Entorno' => app()->environment(),
                'Motor de base de datos' => DB::connection()->getDriverName(),
                'Zona horaria' => config('app.timezone'),
                'Sede activa en sesión' => $sucursalActiva?->nombre ?? 'Todas las Sedes (Global)',
            ],
            'conteos' => [
                'Sucursales activas' => Sucursal::where('activa', true)->count(),
                'Productos registrados' => Producto::count(),
                'Registros de existencia' => Existencia::when($sucursalId !== null, fn ($q) => $q->where('sucursal_id', $sucursalId))->count(),
                'Pedidos pendientes' => Pedido::where('estatus', 'pendiente')->count(),
                'Eventos en bitácora (24h)' => Bitacora::where('created_at', '>=', now()->subDay())->count(),
            ],
        ]);
    }
}
