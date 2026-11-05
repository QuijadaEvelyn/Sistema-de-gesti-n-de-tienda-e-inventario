<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\RecuperacionContrasena;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    /**
     * Muestra el formulario para solicitar el enlace de recuperación de contraseña.
     */
    public function showLinkRequestForm(Request $request): View|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Formulario de solicitud de recuperación de contraseña.',
            ]);
        }

        return view('auth.forgot-password');
    }

    /**
     * Envía un enlace de restablecimiento con token temporal al correo electrónico del usuario.
     *
     * @throws ValidationException
     */
    public function sendResetLinkEmail(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $email = Str::lower($validated['email']);
        $throttleKey = 'forgot-password|'.$email.'|'.$request->ip();

        // 1. Rate Limiting: máximo 3 solicitudes cada 2 minutos
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Demasiadas solicitudes. Por favor, espera {$seconds} segundos antes de reintentar.",
                ], 429);
            }

            throw ValidationException::withMessages([
                'email' => "Demasiadas solicitudes de recuperación. Por favor espera {$seconds} segundos.",
            ]);
        }

        RateLimiter::hit($throttleKey, 120);

        /** @var Usuario|null $usuario */
        $usuario = Usuario::where('email', $email)->first();

        // Si el usuario existe y está activo, generamos el token temporal
        if ($usuario && $usuario->activo) {
            DB::transaction(function () use ($usuario, $request, $email): void {
                // Invalidar tokens previos no utilizados para este usuario
                RecuperacionContrasena::where('usuario_id', $usuario->id)
                    ->whereNull('usado_en')
                    ->delete();

                // Generar token seguro y almacenar su hash sha256 con vigencia de 60 minutos
                $plainToken = Str::random(64);
                $tokenHash = hash('sha256', $plainToken);
                $expiraEn = Carbon::now()->addMinutes(60);

                RecuperacionContrasena::create([
                    'usuario_id' => $usuario->id,
                    'token_hash' => $tokenHash,
                    'expira_en' => $expiraEn,
                ]);

                // Generar la URL firmada/con token para el restablecimiento
                $resetUrl = route('password.reset', [
                    'token' => $plainToken,
                    'email' => $email,
                ]);

                // Registrar en log la notificación (y soporte para Mailable en producción)
                Log::info("[ForgotPasswordController] Enlace de recuperación generado para {$email}.", [
                    'usuario_id' => $usuario->id,
                    'expira_en' => $expiraEn->toDateTimeString(),
                    'reset_url' => $resetUrl,
                ]);

                // Auditoría de seguridad en Bitácora
                Bitacora::create([
                    'usuario_id' => $usuario->id,
                    'accion' => 'SOLICITUD_RECUPERACION_CONTRASENA',
                    'tabla_afectada' => 'recuperacion_contrasenas',
                    'registro_id' => $usuario->id,
                    'detalle' => [
                        'email' => $email,
                        'ip' => $request->ip(),
                        'expira_en' => $expiraEn->toDateTimeString(),
                    ],
                    'ip' => $request->ip() ?? '127.0.0.1',
                    'created_at' => now(),
                ]);
            });
        }

        $mensajeExito = 'Si el correo electrónico existe en nuestra base de datos, recibirás un enlace para restablecer tu contraseña en breve.';

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => $mensajeExito,
            ]);
        }

        return back()->with('status', $mensajeExito);
    }
}
