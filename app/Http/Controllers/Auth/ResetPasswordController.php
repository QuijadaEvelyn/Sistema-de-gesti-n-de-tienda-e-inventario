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
use Illuminate\Support\Facades\Auth;
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
     * Valida el token temporal de verificación, actualiza la contraseña e inicia sesión automáticamente.
     *
     * @throws ValidationException
     */
    public function reset(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
        ], [
            'token.required' => 'El código o token de verificación temporal es obligatorio.',
            'email.required' => 'El correo electrónico es requerido.',
            'email.email' => 'El formato del correo electrónico ingresado no es válido.',
            'password.required' => 'La nueva contraseña es requerida.',
            'password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
            'password.min' => 'La nueva contraseña debe contener al menos 8 caracteres.',
        ]);

        $email = Str::lower($validated['email']);
        $token = trim($validated['token']);
        $tokenHash = hash('sha256', $token);

        /** @var Usuario|null $usuario */
        $usuario = Usuario::where('email', $email)->first();

        if (! $usuario || ! $usuario->activo) {
            throw ValidationException::withMessages([
                'email' => 'No pudimos encontrar una cuenta activa asociada a este correo electrónico.',
            ]);
        }

        /** @var RecuperacionContrasena|null $recuperacion */
        $recuperacion = RecuperacionContrasena::where('usuario_id', $usuario->id)
            ->where('token_hash', $tokenHash)
            ->whereNull('usado_en')
            ->where('expira_en', '>', now())
            ->first();

        if (! $recuperacion) {
            throw ValidationException::withMessages([
                'token' => 'El código o token de verificación es inválido, ha expirado o ya ha sido utilizado.',
                'email' => 'El enlace de recuperación de contraseña es inválido o ha expirado.',
            ]);
        }

        DB::transaction(function () use ($usuario, $recuperacion, $validated, $request, $email): void {
            // 1. Actualizar contraseña cifrada y regenerar remember_token
            $usuario->update([
                'password' => Hash::make($validated['password']),
                'remember_token' => Str::random(60),
            ]);

            // 2. Marcar token actual como consumido
            $recuperacion->update([
                'usado_en' => now(),
            ]);

            // 3. Invalidar cualquier otro token pendiente del usuario para impedir reutilización
            RecuperacionContrasena::where('usuario_id', $usuario->id)
                ->where('id', '!=', $recuperacion->id)
                ->whereNull('usado_en')
                ->delete();

            // 4. Registro de auditoría en Bitácora: Contraseña restablecida
            Bitacora::create([
                'usuario_id' => $usuario->id,
                'accion' => 'CONTRASENA_RESTABLECIDA',
                'tabla_afectada' => 'usuarios',
                'registro_id' => $usuario->id,
                'detalle' => [
                    'email' => $email,
                    'ip' => $request->ip(),
                    'token_id' => $recuperacion->id,
                ],
                'ip' => $request->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);

            // 5. Registro de auditoría en Bitácora: Acceso automático seguro post-recuperación
            Bitacora::create([
                'usuario_id' => $usuario->id,
                'accion' => 'ACCESO_AUTOMATICO_POST_RECUPERACION',
                'tabla_afectada' => 'usuarios',
                'registro_id' => $usuario->id,
                'detalle' => [
                    'email' => $email,
                    'ip' => $request->ip(),
                    'roles' => $usuario->roles->pluck('nombre')->all(),
                ],
                'ip' => $request->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);
        });

        // 6. Autenticación automática continua (sin solicitar re-login)
        Auth::login($usuario, true);
        $request->session()->regenerate();

        // 7. Determinar redirección inmediata por rol de usuario
        $destino = $this->determinarRedireccionPorRol($usuario);
        $mensajeExito = 'Tu contraseña ha sido actualizada con éxito y has iniciado sesión de forma segura en ARCHIVUM.';

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => $mensajeExito,
                'redirect' => $destino,
            ]);
        }

        return redirect()->to($destino)->with('status', $mensajeExito);
    }

    /**
     * Determina la URL de destino según el rol del usuario autenticado.
     */
    protected function determinarRedireccionPorRol(Usuario $user): string
    {
        if ($user->esAdmin()) {
            return route('admin.dashboard');
        }

        if ($user->tieneRol('almacen', 'encargado')) {
            return route('almacen.dashboard');
        }

        if ($user->esVentas()) {
            return route('ventas.dashboard');
        }

        if ($user->esCompras()) {
            return route('compras.dashboard');
        }

        return route('public.home');
    }
}
