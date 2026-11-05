<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class EmpleadoController extends Controller
{
    /**
     * Lista de roles exclusivos para empleados del sistema.
     * Los clientes no se gestionan aquí, ya que se registran de forma pública.
     *
     * @var list<string>
     */
    public const ROLES_EMPLEADOS = [
        'administrador',
        'ventas',
        'almacen',
        'compras',
    ];

    /**
     * Muestra el listado de empleados registrados en el sistema.
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = Usuario::query()
            ->with('roles')
            ->whereHas('roles', function ($q): void {
                $q->whereIn('nombre', self::ROLES_EMPLEADOS);
            });

        // Filtro por término de búsqueda (nombre o correo)
        if ($buscar = $request->input('buscar')) {
            $query->where(function ($q) use ($buscar): void {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('email', 'like', "%{$buscar}%");
            });
        }

        // Filtro por rol específico
        if ($rolFiltro = $request->input('rol')) {
            $query->whereHas('roles', function ($q) use ($rolFiltro): void {
                $q->where('nombre', $rolFiltro);
            });
        }

        // Filtro por estatus de actividad
        if ($request->has('activo')) {
            $query->where('activo', $request->boolean('activo'));
        }

        $empleados = $query->orderBy('nombre')->paginate(15)->withQueryString();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => $empleados,
                'roles_disponibles' => self::ROLES_EMPLEADOS,
            ]);
        }

        return view('admin.empleados.index', [
            'empleados' => $empleados,
            'rolesDisponibles' => self::ROLES_EMPLEADOS,
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva cuenta de empleado.
     */
    public function create(Request $request): View|JsonResponse
    {
        $roles = Rol::whereIn('nombre', self::ROLES_EMPLEADOS)->get();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'roles' => $roles,
            ]);
        }

        return view('admin.empleados.create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Registra un nuevo empleado en el sistema asignándole su rol correspondiente.
     * Exclusivo para administradores. Los empleados no disponen de registro público.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:usuarios,email'],
            'password' => ['required', 'string', Password::min(8)],
            'rol' => ['required', 'string', Rule::in(self::ROLES_EMPLEADOS)],
            'activo' => ['nullable', 'boolean'],
        ]);

        $emailLimpio = Str::lower($validated['email']);

        /** @var Usuario $empleado */
        $empleado = DB::transaction(function () use ($validated, $emailLimpio, $request): Usuario {
            // 1. Crear el usuario con contraseña cifrada
            $usuario = Usuario::create([
                'nombre' => $validated['nombre'],
                'email' => $emailLimpio,
                'password' => Hash::make($validated['password']),
                'activo' => $request->boolean('activo', true),
            ]);

            // 2. Asignar el rol de empleado correspondiente
            $rolModel = Rol::firstOrCreate(
                ['nombre' => $validated['rol']],
                ['descripcion' => 'Rol de personal: '.Str::headline($validated['rol'])]
            );
            $usuario->roles()->attach($rolModel->id);

            // 3. Registrar auditoría en Bitácora
            Bitacora::create([
                'usuario_id' => auth()->id(),
                'accion' => 'EMPLEADO_CREADO',
                'tabla_afectada' => 'usuarios',
                'registro_id' => $usuario->id,
                'detalle' => [
                    'nombre' => $usuario->nombre,
                    'email' => $usuario->email,
                    'rol' => $validated['rol'],
                    'creado_por' => auth()->user()?->email,
                ],
                'ip' => $request->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);

            return $usuario;
        });

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Cuenta de empleado para '{$empleado->nombre}' creada exitosamente con rol '{$validated['rol']}'.",
                'empleado' => [
                    'id' => $empleado->id,
                    'nombre' => $empleado->nombre,
                    'email' => $empleado->email,
                    'rol' => $validated['rol'],
                    'activo' => $empleado->activo,
                ],
            ], 201);
        }

        return redirect()->route('admin.empleados.index')->with(
            'status',
            "Empleado '{$empleado->nombre}' creado exitosamente con rol '{$validated['rol']}'."
        );
    }

    /**
     * Muestra el formulario para editar la información o rol de un empleado.
     */
    public function edit(Request $request, Usuario $empleado): View|JsonResponse
    {
        $empleado->loadMissing('roles');
        $roles = Rol::whereIn('nombre', self::ROLES_EMPLEADOS)->get();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'empleado' => $empleado,
                'roles' => $roles,
            ]);
        }

        return view('admin.empleados.edit', [
            'empleado' => $empleado,
            'roles' => $roles,
        ]);
    }

    /**
     * Actualiza los datos, rol o estatus de actividad de un empleado.
     */
    public function update(Request $request, Usuario $empleado): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('usuarios', 'email')->ignore($empleado->id)],
            'password' => ['nullable', 'string', Password::min(8)],
            'rol' => ['required', 'string', Rule::in(self::ROLES_EMPLEADOS)],
            'activo' => ['required', 'boolean'],
        ]);

        $emailLimpio = Str::lower($validated['email']);

        DB::transaction(function () use ($empleado, $validated, $emailLimpio, $request): void {
            $datosActualizar = [
                'nombre' => $validated['nombre'],
                'email' => $emailLimpio,
                'activo' => (bool) $validated['activo'],
            ];

            if (! empty($validated['password'])) {
                $datosActualizar['password'] = Hash::make($validated['password']);
                $datosActualizar['remember_token'] = Str::random(60);
            }

            $empleado->update($datosActualizar);

            // Sincronizar el rol del empleado
            $rolModel = Rol::firstOrCreate(
                ['nombre' => $validated['rol']],
                ['descripcion' => 'Rol de personal: '.Str::headline($validated['rol'])]
            );
            $empleado->roles()->sync([$rolModel->id]);

            Bitacora::create([
                'usuario_id' => auth()->id(),
                'accion' => 'EMPLEADO_ACTUALIZADO',
                'tabla_afectada' => 'usuarios',
                'registro_id' => $empleado->id,
                'detalle' => [
                    'nombre' => $empleado->nombre,
                    'email' => $empleado->email,
                    'rol' => $validated['rol'],
                    'activo' => $empleado->activo,
                    'actualizado_por' => auth()->user()?->email,
                ],
                'ip' => $request->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);
        });

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Empleado '{$empleado->nombre}' actualizado correctamente.",
            ]);
        }

        return redirect()->route('admin.empleados.index')->with(
            'status',
            "Empleado '{$empleado->nombre}' actualizado correctamente."
        );
    }

    /**
     * Alterna el estatus de activación de un empleado o lo desactiva.
     * Impide que el administrador actual desactive su propia cuenta.
     */
    public function destroy(Request $request, Usuario $empleado): RedirectResponse|JsonResponse
    {
        if ((int) $empleado->id === (int) auth()->id()) {
            $mensajeError = 'No puedes desactivar o eliminar tu propia cuenta de administrador.';

            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => $mensajeError], 403);
            }

            return back()->withErrors(['error' => $mensajeError]);
        }

        DB::transaction(function () use ($empleado, $request): void {
            // Se conmuta el estado de actividad (desactivación lógica segura)
            $nuevoEstado = ! $empleado->activo;
            $empleado->update(['activo' => $nuevoEstado]);

            $accion = $nuevoEstado ? 'EMPLEADO_REACTIVADO' : 'EMPLEADO_DESACTIVADO';

            Bitacora::create([
                'usuario_id' => auth()->id(),
                'accion' => $accion,
                'tabla_afectada' => 'usuarios',
                'registro_id' => $empleado->id,
                'detalle' => [
                    'empleado_email' => $empleado->email,
                    'nuevo_estado_activo' => $nuevoEstado,
                    'modificado_por' => auth()->user()?->email,
                ],
                'ip' => $request->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);
        });

        $mensaje = $empleado->activo
            ? "Empleado '{$empleado->nombre}' reactivado exitosamente."
            : "Empleado '{$empleado->nombre}' desactivado exitosamente.";

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'message' => $mensaje]);
        }

        return redirect()->route('admin.empleados.index')->with('status', $mensaje);
    }
}
