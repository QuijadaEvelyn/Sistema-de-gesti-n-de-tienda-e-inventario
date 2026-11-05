<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Muestra la interfaz de administración del perfil y seguridad de la cuenta.
     */
    public function edit(Request $request): View|JsonResponse
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();
        $usuario->loadMissing(['cliente', 'roles']);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'user' => [
                    'id' => $usuario->id,
                    'nombre' => $usuario->nombre,
                    'email' => $usuario->email,
                    'activo' => $usuario->activo,
                    'roles' => $usuario->roles->pluck('nombre')->toArray(),
                    'cliente' => $usuario->cliente ? [
                        'id' => $usuario->cliente->id,
                        'apellidos' => $usuario->cliente->apellidos,
                        'telefono' => $usuario->cliente->telefono,
                    ] : null,
                ],
            ]);
        }

        return view('auth.profile', [
            'usuario' => $usuario,
            'cliente' => $usuario->cliente,
        ]);
    }

    /**
     * Actualiza la información del perfil del usuario (y datos de cliente si aplica).
     */
    public function update(Request $request): RedirectResponse|JsonResponse
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('usuarios', 'email')->ignore($usuario->id)],
            'apellidos' => ['nullable', 'string', 'max:120'],
            'telefono' => ['nullable', 'string', 'max:20'],
        ]);

        $emailLimpio = Str::lower($validated['email']);

        DB::transaction(function () use ($usuario, $validated, $emailLimpio, $request): void {
            // 1. Actualizar cuenta de Usuario
            $usuario->update([
                'nombre' => $validated['nombre'],
                'email' => $emailLimpio,
            ]);

            // 2. Si el usuario tiene perfil de Cliente vinculado, sincronizar datos
            if ($usuario->cliente) {
                $usuario->cliente->update([
                    'nombre' => $validated['nombre'],
                    'apellidos' => $validated['apellidos'] ?? $usuario->cliente->apellidos,
                    'email' => $emailLimpio,
                    'telefono' => $validated['telefono'] ?? $usuario->cliente->telefono,
                ]);
            }

            // 3. Auditoría en Bitácora
            Bitacora::create([
                'usuario_id' => $usuario->id,
                'accion' => 'PERFIL_ACTUALIZADO',
                'tabla_afectada' => 'usuarios',
                'registro_id' => $usuario->id,
                'detalle' => [
                    'email' => $emailLimpio,
                    'ip' => $request->ip(),
                ],
                'ip' => $request->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);
        });

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Perfil actualizado correctamente.',
                'user' => [
                    'id' => $usuario->id,
                    'nombre' => $usuario->nombre,
                    'email' => $usuario->email,
                ],
            ]);
        }

        return back()->with('status', 'Perfil actualizado correctamente.');
    }

    /**
     * Cambia la contraseña del usuario previa confirmación de la contraseña actual.
     */
    public function updatePassword(Request $request): RedirectResponse|JsonResponse
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', 'confirmed', Password::min(8), 'different:current_password'],
        ]);

        DB::transaction(function () use ($usuario, $validated, $request): void {
            // 1. Cifrar y guardar la nueva contraseña
            $usuario->update([
                'password' => Hash::make($validated['password']),
                'remember_token' => Str::random(60),
            ]);

            // 2. Auditoría en Bitácora
            Bitacora::create([
                'usuario_id' => $usuario->id,
                'accion' => 'CONTRASENA_CAMBIADA',
                'tabla_afectada' => 'usuarios',
                'registro_id' => $usuario->id,
                'detalle' => [
                    'ip' => $request->ip(),
                ],
                'ip' => $request->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);
        });

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Contraseña modificada con éxito.',
            ]);
        }

        return back()->with('status', 'Contraseña modificada con éxito.');
    }
}
