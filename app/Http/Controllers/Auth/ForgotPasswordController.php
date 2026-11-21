<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RecuperacionContrasenaMail;
use App\Models\Bitacora;
use App\Models\RecuperacionContrasena;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class ForgotPasswordController extends Controller
{
    /**
     * Muestra el formulario para solicitar el enlace y código de recuperación de contraseña.
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
     * Envía un correo electrónico con token temporal de verificación y enlace de restablecimiento.
     *
     * @throws ValidationException
     */
    public function sendResetLinkEmail(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
        ], [
            'email.required' => 'Por favor, ingresa tu dirección de correo electrónico.',
            'email.email' => 'La dirección de correo electrónico ingresada no es válida.',
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
                'email' => "Demasiadas solicitudes de recuperación. Por favor espera {$seconds} segundos antes de intentar nuevamente.",
            ]);
        }

        RateLimiter::hit($throttleKey, 120);

        /** @var Usuario|null $usuario */
        $usuario = Usuario::where('email', $email)->first();

        // Si el usuario existe y está activo, generamos el token temporal (en password_reset_tokens y recuperacion_contrasenas) y despachamos el correo real vía SMTP
        if ($usuario && $usuario->activo) {
            try {
                // Generar token de 64 caracteres compatible con el Password Broker nativo de Laravel (password_reset_tokens)
                // y con la tabla de auditoría recuperacion_contrasenas
                $plainToken = Str::random(64);
                $tokenHash = hash('sha256', $plainToken);
                $expiraEn = Carbon::now()->addMinutes(60);

                $resetUrl = route('password.reset', [
                    'token' => $plainToken,
                    'email' => $email,
                ]);

                DB::transaction(function () use ($usuario, $request, $email, $plainToken, $tokenHash, $expiraEn): void {
                    // 1. Registrar el token en la tabla estándar de Laravel `password_reset_tokens`
                    if (Schema::hasTable('password_reset_tokens')) {
                        DB::table('password_reset_tokens')->updateOrInsert(
                            ['email' => $email],
                            [
                                'token' => Hash::make($plainToken),
                                'created_at' => now(),
                            ]
                        );
                    }

                    // 2. Sincronizar en la tabla `recuperacion_contrasenas` del sistema
                    if (Schema::hasTable('recuperacion_contrasenas')) {
                        RecuperacionContrasena::where('usuario_id', $usuario->id)
                            ->whereNull('usado_en')
                            ->delete();

                        RecuperacionContrasena::create([
                            'usuario_id' => $usuario->id,
                            'token_hash' => $tokenHash,
                            'expira_en' => $expiraEn,
                        ]);
                    }

                    // 3. Auditoría de seguridad en Bitácora
                    if (Schema::hasTable('bitacoras')) {
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
                    }
                });

                // Envío real del correo electrónico vía SMTP configurando el Mailable RecuperacionContrasenaMail
                Mail::to($usuario->email)->send(new RecuperacionContrasenaMail($usuario, $plainToken, $resetUrl));

                Log::info("[ForgotPasswordController] Correo de recuperación despachado a {$email}.", [
                    'usuario_id' => $usuario->id,
                    'expira_en' => $expiraEn->toDateTimeString(),
                ]);
            } catch (Throwable $e) {
                Log::error('[ForgotPasswordController] Error al enviar correo SMTP de recuperación: '.$e->getMessage(), [
                    'email' => $email,
                    'exception' => $e,
                ]);

                $mensajeErrorSmtp = 'No pudimos enviar el correo de recuperación en este momento por un problema de conexión con el servidor de correo (SMTP). Por favor verifica tu conexión o intenta de nuevo en unos momentos.';

                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $mensajeErrorSmtp,
                    ], 500);
                }

                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => $mensajeErrorSmtp]);
            }

            $mensajeExito = 'Te enviamos un enlace a tu correo para restablecer tu contraseña. Revisa tu bandeja de entrada o la carpeta de spam.';

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => $mensajeExito,
                    'redirect' => route('password.reset', ['email' => $email]),
                ]);
            }

            if ($request->boolean('stay_on_request')) {
                return back()->with('status', $mensajeExito);
            }

            return redirect()->route('password.reset', ['email' => $email])->with('status', $mensajeExito);
        }

        // Si el usuario no existe o está inactivo, responder sin filtrar existencia de cuentas o notificar según validación
        $mensajeSeguro = 'Te enviamos un enlace a tu correo para restablecer tu contraseña si la dirección está registrada en ARCHIVUM. Revisa tu bandeja de entrada o spam.';

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => $mensajeSeguro,
                'redirect' => route('password.reset', ['email' => $email]),
            ]);
        }

        return redirect()->route('password.reset', ['email' => $email])->with('status', $mensajeSeguro);
    }
}
