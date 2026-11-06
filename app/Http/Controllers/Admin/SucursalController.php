<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Sucursal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SucursalController extends Controller
{
    /**
     * Listado de sucursales y sedes con su volumen de existencias.
     */
    public function index(): View
    {
        return view('admin.sucursales.index', [
            'sucursales' => Sucursal::query()
                ->withCount('existencias')
                ->withSum('existencias as unidades_totales', 'cantidad')
                ->orderBy('nombre')
                ->get(),
        ]);
    }

    /**
     * Registra una nueva sucursal.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150', 'unique:sucursales,nombre'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
        ]);

        $sucursal = Sucursal::create([...$validated, 'activa' => true]);

        $this->registrarBitacora($request, 'SUCURSAL_CREADA', $sucursal);

        return redirect()->route('admin.sucursales')->with('status', "Sucursal '{$sucursal->nombre}' registrada.");
    }

    /**
     * Activa o desactiva una sucursal.
     */
    public function toggle(Request $request, Sucursal $sucursal): RedirectResponse
    {
        $sucursal->update(['activa' => ! $sucursal->activa]);

        $this->registrarBitacora($request, $sucursal->activa ? 'SUCURSAL_ACTIVADA' : 'SUCURSAL_DESACTIVADA', $sucursal);

        return redirect()->route('admin.sucursales')->with(
            'status',
            "Sucursal '{$sucursal->nombre}' ".($sucursal->activa ? 'activada' : 'desactivada').'.'
        );
    }

    /**
     * Registra el evento de auditoría de la sucursal.
     */
    protected function registrarBitacora(Request $request, string $accion, Sucursal $sucursal): void
    {
        Bitacora::create([
            'usuario_id' => $request->user()->id,
            'accion' => $accion,
            'tabla_afectada' => 'sucursales',
            'registro_id' => $sucursal->id,
            'detalle' => ['nombre' => $sucursal->nombre, 'activa' => $sucursal->activa],
            'ip' => $request->ip() ?? '127.0.0.1',
            'created_at' => now(),
        ]);
    }
}
