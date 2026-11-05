<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\RecuperacionContrasena;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    /**
     * Muestra el formulario para ingresar la nueva contraseña usando el token temporal.
     */
    public function showResetForm(Request $request, ?string $token = null): View|JsonResponse
    {
        $token = $token ?? (string) $request->query('token', '');
        $email = (string) $request->query('email', '');

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Formulario de restablecimiento de contraseña.',
                'token' => $token,
                'email' => $email,
            ]);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Valida el token temporal de expiración y actualiza la contraseña del usuario.
     *
     * @throws ValidationException
     */
    public function reset(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
        ]);

        $email = Str::lower($validated['email']);
        $tokenHash = hash('sha256', $validated['token']);

        /** @var Usuario|null $usuario */
        $usuario = Usuario::where('email', $email)->first();

        if (! $usuario) {
            throw ValidationException::withMessages([
                'email' => 'No pudimos encontrar un usuario con ese correo electrónico.',
            ]);
        }

        // Buscar el token temporal activo y no expirado con bloqueo pesimista
        /** @var RecuperacionContrasena|null $recuperacion */
        $recuperacion = RecuperacionContrasena::where('usuario_id', $usuario->id)
            ->where('token_hash', $tokenHash)
            ->whereNull('usado_en')
            ->where('expira_en', '>', now())
            ->first();

        if (! $recuperacion) {
            throw ValidationException::withMessages([
                'email' => 'El enlace de recuperación de contraseña es inválido o ha expirado.',
            ]);
        }

        DB::transaction(function () use ($usuario, $recuperacion, $validated, $request, $email): void {
            // 1. Actualizar contraseña con Hash::make
            $usuario->update([
                'password' => Hash::make($validated['password']),
                'remember_token' => Str::random(60),
            ]);

            // 2. Marcar token como consumido
            $recuperacion->update([
                'usado_en' => now(),
            ]);

            // 3. Auditoría de seguridad en Bitácora
            Bitacora::create([
                'usuario_id' => $usuario->id,
                'accion' => 'CONTRASENA_RESTABLECIDA',
                'tabla_afectada' => 'usuarios',
                'registro_id' => $usuario->id,
                'detalle' => [
                    'email' => $email,
                    'ip' => $request->ip(),
                ],
                'ip' => $request->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);
        });

        $mensajeExito = 'Tu contraseña ha sido restablecida exitosamente. Ya puedes iniciar sesión con tu nueva clave.';

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => $mensajeExito,
            ]);
        }

        return redirect()->route('login')->with('status', $mensajeExito);
    }
}
