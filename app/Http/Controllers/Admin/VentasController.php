<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Devolucion;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Promocion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

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
     * Solicitudes de devolución.
     */
    public function devoluciones(Request $request): View
    {
        return view('admin.ventas.index', [
            'seccion' => 'devoluciones',
            'registros' => Devolucion::query()
                ->with(['pedido', 'cliente'])
                ->when($request->filled('estatus'), fn (Builder $q) => $q->where('estatus', $request->string('estatus')))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
        ]);
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
