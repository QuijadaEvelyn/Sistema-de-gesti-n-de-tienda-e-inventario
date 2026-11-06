<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BitacoraController extends Controller
{
    /**
     * Bitácora de auditoría del sistema con filtros por acción, tabla y fecha.
     */
    public function index(Request $request): View
    {
        $eventos = Bitacora::query()
            ->with('usuario')
            ->when($request->filled('accion'), fn (Builder $q) => $q->where('accion', 'like', '%'.$request->string('accion')->trim()->upper().'%'))
            ->when($request->filled('tabla'), fn (Builder $q) => $q->where('tabla_afectada', $request->string('tabla')))
            ->when($request->filled('desde'), fn (Builder $q) => $q->whereDate('created_at', '>=', $request->date('desde')))
            ->latest('created_at')
            ->paginate(30)
            ->withQueryString();

        return view('admin.bitacora.index', [
            'eventos' => $eventos,
            'tablas' => Bitacora::query()->whereNotNull('tabla_afectada')->distinct()->orderBy('tabla_afectada')->pluck('tabla_afectada'),
        ]);
    }
}
