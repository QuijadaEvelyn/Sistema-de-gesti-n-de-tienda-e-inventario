<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use Illuminate\View\View;

class RolController extends Controller
{
    /**
     * Matriz de permisos por módulo. Refleja exactamente las restricciones
     * aplicadas por el middleware `rol` en routes/web.php y por el sidebar.
     *
     * @var array<string, list<string>>
     */
    public const MATRIZ_PERMISOS = [
        'Dashboard General' => ['administrador'],
        'Inventario Unificado' => ['administrador', 'almacen', 'compras'],
        'Ajustes, Recepciones y Transferencias' => ['administrador', 'almacen'],
        'Administración de Ventas' => ['administrador', 'ventas'],
        'Proveedores y Órdenes de Compra' => ['administrador', 'compras'],
        'Alta / Baja de Empleados' => ['administrador'],
        'Sucursales & Sedes' => ['administrador'],
        'Bitácora del Sistema' => ['administrador'],
        'Roles y Permisos' => ['administrador'],
    ];

    /**
     * Roles registrados, número de usuarios por rol y matriz de permisos.
     */
    public function index(): View
    {
        return view('admin.roles.index', [
            'roles' => Rol::query()->withCount('usuarios')->orderBy('nombre')->get(),
            'matriz' => self::MATRIZ_PERMISOS,
            'columnas' => ['administrador', 'ventas', 'almacen', 'compras'],
        ]);
    }
}
