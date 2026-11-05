<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Carrito;
use App\Models\Cliente;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Muestra el formulario público de registro de clientes.
     */
    public function showRegistrationForm(Request $request): View|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Formulario de registro de nuevo cliente de ARCHIVUM.',
            ]);
        }

        return view('auth.register');
    }

    /**
     * Procesa el registro de un nuevo cliente, asignando el rol 'cliente' por defecto
     * y creando automáticamente su perfil de cliente y su carrito de compras personal.
     */
    public function register(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'apellidos' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:usuarios,email', 'unique:clientes,email'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
            'telefono' => ['nullable', 'string', 'max:20'],
        ]);

        /** @var Usuario $usuario */
        $usuario = DB::transaction(function () use ($validated, $request): Usuario {
            // 1. Crear la cuenta de Usuario con contraseña cifrada (Hash::make)
            $usuario = Usuario::create([
                'nombre' => $validated['nombre'],
                'email' => Str::lower($validated['email']),
                'password' => Hash::make($validated['password']),
                'activo' => true,
            ]);

            // 2. Asignar por defecto el rol 'cliente'
            $rolCliente = Rol::firstOrCreate(
                ['nombre' => 'cliente'],
                ['descripcion' => 'Cliente de la tienda y catálogo público']
            );
            $usuario->roles()->attach($rolCliente->id);

            // 3. Crear el perfil de Cliente vinculado a la cuenta
            $cliente = Cliente::create([
                'usuario_id' => $usuario->id,
                'nombre' => $validated['nombre'],
                'apellidos' => $validated['apellidos'],
                'email' => Str::lower($validated['email']),
                'telefono' => $validated['telefono'] ?? null,
            ]);

            // 4. Inicializar el carrito personal del nuevo cliente
            Carrito::create([
                'cliente_id' => $cliente->id,
            ]);

            // 5. Registro de auditoría en Bitácora
            Bitacora::create([
                'usuario_id' => $usuario->id,
                'accion' => 'CLIENTE_REGISTRADO',
                'tabla_afectada' => 'usuarios',
                'registro_id' => $usuario->id,
                'detalle' => [
                    'email' => $usuario->email,
                    'cliente_id' => $cliente->id,
                    'rol' => 'cliente',
                ],
                'ip' => $request->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);

            return $usuario;
        });

        // 6. Inicio de sesión automático, regeneración de sesión y limpieza de intended
        Auth::login($usuario);
        $request->session()->regenerate();
        $request->session()->forget('url.intended');

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Registro completado exitosamente. ¡Bienvenido a ARCHIVUM!',
                'user' => [
                    'id' => $usuario->id,
                    'nombre' => $usuario->nombre,
                    'email' => $usuario->email,
                    'roles' => ['cliente'],
                ],
                'redirect' => route('public.home'),
            ], 201);
        }

        return redirect()->route('public.home')->with('status', '¡Registro completado con éxito! Bienvenido a ARCHIVUM.');
    }
}
