<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Envio;
use App\Models\Existencia;
use App\Models\MovimientoInventario;
use App\Models\Sucursal;
use App\Models\VarianteSku;
use App\Services\TransferenciaStockService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class InventarioController extends Controller
{
    /**
     * Inventario Unificado: existencias por sucursal con filtros de búsqueda,
     * sucursal y alerta de stock mínimo.
     */
    public function index(Request $request): View
    {
        $existencias = Existencia::query()
            ->with(['varianteSku.producto', 'sucursal'])
            ->when($request->filled('sucursal_id'), fn (Builder $q) => $q->where('sucursal_id', $request->integer('sucursal_id')))
            ->when($request->boolean('bajo_stock'), fn (Builder $q) => $q->whereColumn('cantidad', '<=', 'stock_minimo'))
            ->when($request->filled('buscar'), function (Builder $q) use ($request): void {
                $termino = '%'.$request->string('buscar')->trim().'%';
                $q->whereHas('varianteSku', function (Builder $sku) use ($termino): void {
                    $sku->where('sku', 'like', $termino)
                        ->orWhere('descripcion', 'like', $termino)
                        ->orWhereHas('producto', fn (Builder $p) => $p->where('nombre', 'like', $termino));
                });
            })
            ->orderBy('sucursal_id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.inventario.index', [
            'existencias' => $existencias,
            'sucursales' => Sucursal::orderBy('nombre')->get(),
            'totalUnidades' => (int) Existencia::sum('cantidad'),
            'alertasStock' => Existencia::whereColumn('cantidad', '<=', 'stock_minimo')->count(),
            'puedeAjustar' => $request->user()->can('gestionar-almacen'),
        ]);
    }

    /**
     * Historial de movimientos de inventario con formulario de ajuste / recepción.
     */
    public function movimientos(Request $request): View
    {
        $movimientos = MovimientoInventario::query()
            ->with(['existencia.varianteSku.producto', 'existencia.sucursal', 'usuario'])
            ->when($request->filled('tipo'), fn (Builder $q) => $q->where('tipo', $request->string('tipo')))
            ->latest('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('admin.inventario.movimientos', [
            'movimientos' => $movimientos,
            'existencias' => Existencia::with(['varianteSku', 'sucursal'])->orderBy('sucursal_id')->get(),
            'tipoPreseleccionado' => $request->string('tipo')->toString() ?: 'ajuste',
        ]);
    }

    /**
     * Registra un ajuste manual de stock o una recepción de mercancía.
     */
    public function registrarAjuste(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'existencia_id' => ['required', 'integer', 'exists:existencias,id'],
            'tipo' => ['required', Rule::in(['entrada', 'salida', 'ajuste'])],
            'cantidad' => ['required', 'integer', 'min:1'],
            'motivo' => ['required', 'string', 'max:255'],
        ]);

        try {
            DB::transaction(function () use ($validated, $request): void {
                /** @var Existencia $existencia */
                $existencia = Existencia::query()->lockForUpdate()->findOrFail($validated['existencia_id']);
                $cantidad = (int) $validated['cantidad'];

                // 'ajuste' fija la existencia al valor capturado; 'entrada' suma; 'salida' resta.
                $delta = match ($validated['tipo']) {
                    'entrada' => $cantidad,
                    'salida' => -$cantidad,
                    'ajuste' => $cantidad - $existencia->cantidad,
                };

                if ($existencia->cantidad + $delta < 0) {
                    throw new RuntimeException("Stock insuficiente: solo hay {$existencia->cantidad} unidades disponibles.");
                }

                $existencia->increment('cantidad', $delta);

                $movimiento = MovimientoInventario::create([
                    'existencia_id' => $existencia->id,
                    'usuario_id' => $request->user()->id,
                    'tipo' => $validated['tipo'],
                    'cantidad' => $delta,
                    'motivo' => $validated['motivo'],
                    'created_at' => now(),
                ]);

                Bitacora::create([
                    'usuario_id' => $request->user()->id,
                    'accion' => 'INVENTARIO_'.strtoupper($validated['tipo']),
                    'tabla_afectada' => 'movimientos_inventario',
                    'registro_id' => $movimiento->id,
                    'detalle' => ['existencia_id' => $existencia->id, 'delta' => $delta, 'stock_resultante' => $existencia->cantidad],
                    'ip' => $request->ip() ?? '127.0.0.1',
                    'created_at' => now(),
                ]);
            });
        } catch (RuntimeException $exception) {
            return back()->withInput()->withErrors(['cantidad' => $exception->getMessage()]);
        }

        return redirect()->route('almacen.ajustes')->with('status', 'Movimiento de inventario registrado correctamente.');
    }

    /**
     * Transferencias entre sucursales: envíos en tránsito y formulario de nuevo envío.
     */
    public function transferencias(): View
    {
        $salidas = MovimientoInventario::query()
            ->with(['existencia.varianteSku', 'existencia.sucursal', 'usuario'])
            ->where('tipo', 'traspaso')
            ->where('referencia_type', Sucursal::class)
            ->latest('created_at')
            ->limit(50)
            ->get();

        $recibidas = MovimientoInventario::query()
            ->where('tipo', 'traspaso')
            ->where('referencia_type', MovimientoInventario::class)
            ->whereIn('referencia_id', $salidas->pluck('id'))
            ->pluck('referencia_id')
            ->all();

        return view('admin.inventario.transferencias', [
            'salidas' => $salidas,
            'recibidas' => $recibidas,
            'sucursales' => Sucursal::where('activa', true)->orderBy('nombre')->get(),
            'variantes' => VarianteSku::where('activa', true)->orderBy('sku')->get(),
        ]);
    }

    /**
     * Registra el envío de una transferencia (descuenta del origen; queda en tránsito).
     */
    public function registrarTransferencia(Request $request, TransferenciaStockService $transferencias): RedirectResponse
    {
        $validated = $request->validate([
            'sucursal_origen_id' => ['required', 'integer', 'exists:sucursales,id'],
            'sucursal_destino_id' => ['required', 'integer', 'exists:sucursales,id', 'different:sucursal_origen_id'],
            'variante_sku_id' => ['required', 'integer', 'exists:variantes_sku,id'],
            'cantidad' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $transferencias->registrarEnvioTransferencia(
                (int) $validated['sucursal_origen_id'],
                (int) $validated['sucursal_destino_id'],
                [['variante_sku_id' => (int) $validated['variante_sku_id'], 'cantidad' => (int) $validated['cantidad']]],
                $request->user(),
            );
        } catch (RuntimeException $exception) {
            return back()->withInput()->withErrors(['cantidad' => $exception->getMessage()]);
        }

        return redirect()->route('almacen.transferencias')->with('status', 'Transferencia enviada. El stock queda en tránsito hasta confirmar la recepción.');
    }

    /**
     * Confirma la recepción de una transferencia en la sucursal destino.
     */
    public function confirmarTransferencia(Request $request, MovimientoInventario $movimiento, TransferenciaStockService $transferencias): RedirectResponse
    {
        $yaRecibida = MovimientoInventario::query()
            ->where('tipo', 'traspaso')
            ->where('referencia_type', MovimientoInventario::class)
            ->where('referencia_id', $movimiento->id)
            ->exists();

        if ($yaRecibida || $movimiento->referencia_type !== Sucursal::class) {
            return back()->withErrors(['transferencia' => 'Esta transferencia ya fue recibida o no es un envío válido.']);
        }

        $transferencias->confirmarRecepcionTransferencia($movimiento->id, $request->user()->id);

        return redirect()->route('almacen.transferencias')->with('status', 'Recepción de transferencia confirmada.');
    }

    /**
     * Envíos en preparación / tránsito para el área de almacén.
     */
    public function envios(Request $request): View
    {
        $envios = Envio::query()
            ->with(['pedido', 'sucursal'])
            ->when($request->filled('estado'), fn (Builder $q) => $q->where('estado_actual', $request->string('estado')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.inventario.envios', ['envios' => $envios]);
    }
}
