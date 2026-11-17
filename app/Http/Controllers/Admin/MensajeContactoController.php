<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\MensajeContacto;
use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class MensajeContactoController extends Controller
{
    /**
     * Bandeja de mensajes y consultas públicas remitidas a curaduría.
     */
    public function index(Request $request): View
    {
        $query = MensajeContacto::query()->with('sucursal');

        if ($request->filled('buscar')) {
            $buscar = $request->string('buscar')->trim()->value();
            $query->where(function (Builder $q) use ($buscar): void {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('email', 'like', "%{$buscar}%")
                    ->orWhere('asunto', 'like', "%{$buscar}%")
                    ->orWhere('mensaje', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('estatus') && in_array($request->string('estatus')->value(), ['pendiente', 'leido', 'respondido'], true)) {
            $query->where('estatus', $request->string('estatus')->value());
        }

        if ($request->filled('sucursal_id')) {
            $query->where('sucursal_id', $request->integer('sucursal_id'));
        }

        $mensajes = $query->latest('created_at')->paginate(15)->withQueryString();

        $sucursales = Schema::hasTable('sucursales')
            ? Sucursal::where('activa', true)->orderBy('nombre')->get()
            : collect();

        $totales = [
            'total' => MensajeContacto::count(),
            'pendientes' => MensajeContacto::where('estatus', 'pendiente')->count(),
            'leidos' => MensajeContacto::where('estatus', 'leido')->count(),
            'respondidos' => MensajeContacto::where('estatus', 'respondido')->count(),
        ];

        return view('admin.mensajes.index', [
            'mensajes' => $mensajes,
            'sucursales' => $sucursales,
            'totales' => $totales,
            'filtros' => [
                'buscar' => $request->string('buscar')->value(),
                'estatus' => $request->string('estatus')->value(),
                'sucursal_id' => $request->string('sucursal_id')->value(),
            ],
        ]);
    }

    /**
     * Actualiza el estatus de un mensaje de contacto y audita la acción en bitácora.
     */
    public function actualizarEstatus(Request $request, MensajeContacto $mensaje): RedirectResponse
    {
        $validated = $request->validate([
            'estatus' => ['required', 'string', 'in:pendiente,leido,respondido'],
        ], [
            'estatus.required' => 'El estatus es obligatorio.',
            'estatus.in' => 'El estatus debe ser pendiente, leido o respondido.',
        ]);

        $estatusAnterior = $mensaje->estatus;
        $nuevoEstatus = $validated['estatus'];

        $mensaje->update([
            'estatus' => $nuevoEstatus,
        ]);

        if (Schema::hasTable('bitacoras')) {
            Bitacora::create([
                'usuario_id' => auth()->id(),
                'accion' => 'MENSAJE_CONTACTO_ESTATUS_ACTUALIZADO',
                'tabla_afectada' => 'mensajes_contacto',
                'registro_id' => $mensaje->id,
                'detalle' => [
                    'mensaje_id' => $mensaje->id,
                    'remitente' => $mensaje->nombre,
                    'email' => $mensaje->email,
                    'asunto' => $mensaje->asunto,
                    'estatus_anterior' => $estatusAnterior,
                    'estatus_nuevo' => $nuevoEstatus,
                ],
                'ip' => $request->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);
        }

        $etiquetas = [
            'pendiente' => 'Pendiente',
            'leido' => 'Leído',
            'respondido' => 'Respondido',
        ];

        return redirect()->back()->with(
            'status',
            "La consulta #{$mensaje->id} de {$mensaje->nombre} fue marcada como \"{$etiquetas[$nuevoEstatus]}\" exitosamente."
        );
    }
}
