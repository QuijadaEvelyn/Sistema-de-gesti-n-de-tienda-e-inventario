<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Existencia;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Punto de entrada del panel /admin (admin.dashboard).
     *
     * Renderiza el panel administrativo principal adaptando el menú lateral
     * según los permisos del usuario autenticado (Administrador, Ventas, Almacén o Compras).
     */
    public function index(Request $request): View
    {
        /** @var Usuario|null $usuario */
        $usuario = $request->user();

        return view('admin.dashboard', [
            'usuario' => $usuario?->loadMissing('roles'),
        ]);
    }

    /**
     * Estado de la Terminal ARCHIVUM (indicador "TERMINAL ARCHIVO v4.8.2-PROD").
     * Muestra información técnica del sistema y de la sesión del operador.
     */
    public function terminal(Request $request): View
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();

        return view('admin.terminal', [
            'version' => config('archivum.version', 'v4.8.2-PROD'),
            'usuario' => $usuario->loadMissing('roles'),
            'sistema' => [
                'PHP' => PHP_VERSION,
                'Laravel' => app()->version(),
                'Entorno' => app()->environment(),
                'Motor de base de datos' => DB::connection()->getDriverName(),
                'Zona horaria' => config('app.timezone'),
            ],
            'conteos' => [
                'Sucursales activas' => Sucursal::where('activa', true)->count(),
                'Productos registrados' => Producto::count(),
                'Registros de existencia' => Existencia::count(),
                'Pedidos pendientes' => Pedido::where('estatus', 'pendiente')->count(),
                'Eventos en bitácora (24h)' => Bitacora::where('created_at', '>=', now()->subDay())->count(),
            ],
        ]);
    }
}
