<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Devolucion;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Promocion;
use App\Models\Sucursal;
use App\Services\DevolucionService;
use App\Services\SucursalContextoService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class VentasController extends Controller
{
    /**
     * Transiciones de estatus permitidas desde el módulo de Ventas.
     *
     * @var array<string, list<string>>
     */
    public const TRANSICIONES = [
        'pendiente' => ['pagado', 'cancelado'],
        'pagado' => ['cancelado'],
        'enviado' => [],
        'entregado' => [],
        'cancelado' => [],
    ];

    /**
     * Listado de pedidos con filtros por estatus y folio.
     */
    public function pedidos(Request $request): View
    {
        $pedidos = Pedido::query()
            ->with('cliente')
            ->when($request->filled('estatus'), fn (Builder $q) => $q->where('estatus', $request->string('estatus')))
            ->when($request->filled('buscar'), fn (Builder $q) => $q->where('folio', 'like', '%'.$request->string('buscar')->trim().'%'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.ventas.index', [
            'seccion' => 'pedidos',
            'registros' => $pedidos,
            'resumen' => Pedido::query()->select('estatus', DB::raw('count(*) as total'))->groupBy('estatus')->pluck('total', 'estatus'),
            'transiciones' => self::TRANSICIONES,
        ]);
    }

    /**
     * Detalle de un pedido con partidas, pagos, envíos y devoluciones.
     */
    public function show(Pedido $pedido): View
    {
        $pedido->load(['cliente', 'detalles.varianteSku.producto', 'pagos', 'envios', 'devoluciones']);

        return view('admin.ventas.show', [
            'pedido' => $pedido,
            'transicionesDisponibles' => self::TRANSICIONES[$pedido->estatus] ?? [],
        ]);
    }

    /**
     * Confirma (marca como pagado) o cancela un pedido.
     */
    public function actualizarEstatus(Request $request, Pedido $pedido): RedirectResponse
    {
        $permitidos = self::TRANSICIONES[$pedido->estatus] ?? [];

        $validated = $request->validate([
            'estatus' => ['required', Rule::in($permitidos)],
        ], [
            'estatus.in' => "No es posible cambiar un pedido '{$pedido->estatus}' al estatus solicitado.",
        ]);

        DB::transaction(function () use ($pedido, $validated, $request): void {
            $anterior = $pedido->estatus;
            $pedido->update(['estatus' => $validated['estatus']]);

            Bitacora::create([
                'usuario_id' => $request->user()->id,
                'accion' => 'PEDIDO_'.strtoupper($validated['estatus']),
                'tabla_afectada' => 'pedidos',
                'registro_id' => $pedido->id,
                'detalle' => ['folio' => $pedido->folio, 'estatus_anterior' => $anterior, 'estatus_nuevo' => $validated['estatus']],
                'ip' => $request->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);
        });

        return back()->with('status', "Pedido {$pedido->folio} actualizado a '{$validated['estatus']}'.");
    }

    /**
     * Pagos registrados (OXXO, transferencia, tarjeta) pendientes de validación.
     */
    public function pagos(Request $request): View
    {
        return view('admin.ventas.index', [
            'seccion' => 'pagos',
            'registros' => Pago::query()
                ->with('pedido')
                ->when($request->filled('estatus'), fn (Builder $q) => $q->where('estatus', $request->string('estatus')))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    /**
     * Panel de Gestión de Devoluciones y Garantías por Defectos.
     */
    public function devoluciones(Request $request): View
    {
        $sucursalActiva = app(SucursalContextoService::class)->resolverSucursalAdmin($request, $request->user());
        $sucursales = Sucursal::query()->where('activa', true)->orderBy('id')->get();
        $filtroEstado = $request->filled('etapa')
            ? $request->string('etapa')->toString()
            : ($request->filled('estatus') ? $request->string('estatus')->toString() : null);

        $query = Devolucion::query()
            ->with([
                'pedido.envios.sucursal',
                'cliente',
                'detallePedido.varianteSku.producto',
                'sucursalDestino',
                'revisor',
            ])
            ->when($filtroEstado !== null && $filtroEstado !== '', function (Builder $q) use ($filtroEstado): void {
                $q->where(function (Builder $sub) use ($filtroEstado): void {
                    $sub->where('etapa_devolucion', $filtroEstado)
                        ->orWhere('estatus', $filtroEstado);
                });
            })
            ->when($request->filled('buscar'), function (Builder $q) use ($request): void {
                $termino = '%'.$request->string('buscar')->trim().'%';
                $q->where(function (Builder $sub) use ($termino): void {
                    $sub->where('folio_devolucion', 'like', $termino)
                        ->orWhere('guia_retorno', 'like', $termino)
                        ->orWhereHas('pedido', fn (Builder $p) => $p->where('folio', 'like', $termino));
                });
            });

        $todas = Devolucion::query()->get();
        $resumenDevoluciones = [
            'solicitada' => $todas->filter(fn (Devolucion $d) => $d->estadoOperativo() === 'solicitada')->count(),
            'aprobada' => $todas->filter(fn (Devolucion $d) => $d->estadoOperativo() === 'aprobada')->count(),
            'rechazada' => $todas->filter(fn (Devolucion $d) => $d->estadoOperativo() === 'rechazada')->count(),
            'producto_retornado' => $todas->filter(fn (Devolucion $d) => $d->estadoOperativo() === 'producto_retornado')->count(),
            'reembolso_procesado' => $todas->filter(fn (Devolucion $d) => $d->estadoOperativo() === 'reembolso_procesado')->count(),
        ];

        return view('admin.ventas.index', [
            'seccion' => 'devoluciones',
            'registros' => $query->latest()->paginate(20)->withQueryString(),
            'resumenDevoluciones' => $resumenDevoluciones,
            'etapasDevolucion' => Devolucion::ETAPAS,
            'motivosDevolucion' => Devolucion::MOTIVOS_CATALOGO,
            'filtroEstado' => $filtroEstado,
            'sucursales' => $sucursales,
            'sucursalActiva' => $sucursalActiva,
        ]);
    }

    /**
     * Autoriza o rechaza una solicitud de devolución entrante y genera la guía/indicaciones de retorno.
     */
    public function dictaminarDevolucion(Request $request, Devolucion $devolucion): RedirectResponse
    {
        $validated = $request->validate([
            'decision' => ['required', Rule::in(['aprobar', 'rechazar'])],
            'dictamen_admin' => ['nullable', 'string', 'max:1500'],
            'guia_retorno' => ['nullable', 'string', 'max:50'],
            'instrucciones_retorno' => ['nullable', 'string', 'max:1500'],
        ]);

        try {
            $actualizada = app(DevolucionService::class)->dictaminarSolicitud(
                devolucion: $devolucion,
                decision: (string) $validated['decision'],
                dictamenAdmin: $validated['dictamen_admin'] ?? null,
                guiaRetorno: $validated['guia_retorno'] ?? null,
                instruccionesRetorno: $validated['instrucciones_retorno'] ?? null,
                usuarioId: (int) $request->user()->id
            );
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withErrors(['devolucion' => $e->getMessage()]);
        }

        $mensaje = $actualizada->estatus === 'aprobada'
            ? "Devolución {$actualizada->folio_devolucion} APROBADA. Guía de retorno emitida: {$actualizada->guia_retorno}."
            : "Devolución {$actualizada->folio_devolucion} RECHAZADA.";

        return back()->with('status', $mensaje);
    }

    /**
     * Registra la recepción física del artículo devuelto y actualiza las existencias
     * (reintegrando la pieza al stock de la sucursal con su graduación o enviándola a mermas/baja).
     */
    public function procesarRecepcionDevolucion(Request $request, Devolucion $devolucion): RedirectResponse
    {
        $validated = $request->validate([
            'destino_inventario' => ['required', Rule::in(['reintegrar_stock', 'merma_baja'])],
            'sucursal_destino_id' => ['nullable', 'integer', 'exists:sucursales,id'],
            'nueva_graduacion' => ['nullable', 'string', 'max:120'],
            'dictamen_admin' => ['nullable', 'string', 'max:1500'],
            'procesar_reembolso' => ['nullable', 'boolean'],
        ]);

        try {
            $procesarReembolso = $request->has('procesar_reembolso')
                ? $request->boolean('procesar_reembolso')
                : true;

            $actualizada = app(DevolucionService::class)->procesarRetornoFisicoYStock(
                devolucion: $devolucion,
                destinoInventario: (string) $validated['destino_inventario'],
                sucursalDestinoId: isset($validated['sucursal_destino_id']) ? (int) $validated['sucursal_destino_id'] : null,
                nuevaGraduacion: $validated['nueva_graduacion'] ?? null,
                dictamenAdmin: $validated['dictamen_admin'] ?? null,
                procesarReembolso: $procesarReembolso,
                usuarioId: (int) $request->user()->id
            );
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withErrors(['devolucion' => $e->getMessage()]);
        }

        $accionStock = $actualizada->destino_inventario === 'reintegrar_stock'
            ? 'reintegrada al stock de la sucursal'
            : 'enviada a mermas / baja de inventario';

        return back()->with(
            'status',
            "Recepción física de {$actualizada->folio_devolucion} procesada: pieza {$accionStock} ({$actualizada->etiquetaEstado()})."
        );
    }

    /**
     * Promociones y descuentos vigentes / programados.
     */
    public function promociones(): View
    {
        return view('admin.ventas.index', [
            'seccion' => 'promociones',
            'registros' => Promocion::query()->orderByDesc('activa')->orderByDesc('inicia_en')->paginate(20),
        ]);
    }
}
