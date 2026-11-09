<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Producto;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión unificado dentro del Drawer Lateral sobre la tienda pública.
     */
    public function showLoginForm(Request $request): View|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Formulario de inicio de sesión de ARCHIVUM.',
            ]);
        }

        $productos = Schema::hasTable('productos')
            ? Producto::with(['catalogo', 'libro', 'disco', 'coleccionable', 'variantesSku.existencias'])
                ->where('activo', true)
                ->orderByDesc('precio')
                ->get()
            : collect();

        return view('auth.login', [
            'productos' => $productos,
            'drawerAutoOpen' => true,
            'drawerDefaultTab' => 'login',
        ]);
    }

    /**
     * Muestra el portal de acceso para personal interno unificado con el Drawer Lateral.
     */
    public function showAdminLoginForm(Request $request): View|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Portal de acceso para personal interno de ARCHIVUM.',
            ]);
        }

        $productos = Schema::hasTable('productos')
            ? Producto::with(['catalogo', 'libro', 'disco', 'coleccionable', 'variantesSku.existencias'])
                ->where('activo', true)
                ->orderByDesc('precio')
                ->get()
            : collect();

        return view('auth.admin-login', [
            'productos' => $productos,
            'drawerAutoOpen' => true,
            'drawerDefaultTab' => 'login',
        ]);
    }

    /**
     * Procesa la solicitud de inicio de sesión con limitación de intentos (Throttle)
     * y verificación de cuenta activa.
     *
     * @throws ValidationException
     */
    public function login(Request $request): RedirectResponse|JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $throttleKey = $this->throttleKey($request);

        // 1. Verificación de Rate Limiting (máximo 5 intentos antes del bloqueo)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            Bitacora::create([
                'usuario_id' => null,
                'accion' => 'LOGIN_BLOQUEADO_POR_THROTTLE',
                'tabla_afectada' => 'usuarios',
                'registro_id' => null,
                'detalle' => [
                    'email' => $credentials['email'],
                    'ip' => $request->ip(),
                    'segundos_espera' => $seconds,
                ],
                'ip' => $request->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Demasiados intentos de acceso. Por favor, reintenta en {$seconds} segundos.",
                ], 429);
            }

            throw ValidationException::withMessages([
                'email' => "Demasiados intentos de acceso fallidos. Por favor, reintenta en {$seconds} segundos.",
            ]);
        }

        $remember = $request->boolean('remember');

        // 2. Intento de autenticación
        if (! Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $remember)) {
            RateLimiter::hit($throttleKey, 60);

            Bitacora::create([
                'usuario_id' => null,
                'accion' => 'LOGIN_FALLIDO',
                'tabla_afectada' => 'usuarios',
                'registro_id' => null,
                'detalle' => [
                    'email' => $credentials['email'],
                    'ip' => $request->ip(),
                ],
                'ip' => $request->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
                ], 422);
            }

            throw ValidationException::withMessages([
                'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
            ]);
        }

        /** @var Usuario $user */
        $user = Auth::user();

        // 3. Verificación de estado de la cuenta
        if (! $user->activo) {
            Auth::logout();
            RateLimiter::hit($throttleKey, 60);

            Bitacora::create([
                'usuario_id' => $user->id,
                'accion' => 'LOGIN_CUENTA_INACTIVA',
                'tabla_afectada' => 'usuarios',
                'registro_id' => $user->id,
                'detalle' => [
                    'email' => $user->email,
                    'ip' => $request->ip(),
                ],
                'ip' => $request->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tu cuenta se encuentra inactiva. Por favor, contacta al administrador.',
                ], 403);
            }

            throw ValidationException::withMessages([
                'email' => 'Tu cuenta se encuentra inactiva. Por favor, contacta al administrador del sistema.',
            ]);
        }

        // 4. Limpieza del limitador y regeneración de sesión contra Session Fixation
        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        Bitacora::create([
            'usuario_id' => $user->id,
            'accion' => 'LOGIN_EXITOSO',
            'tabla_afectada' => 'usuarios',
            'registro_id' => $user->id,
            'detalle' => [
                'email' => $user->email,
                'roles' => $user->roles->pluck('nombre')->toArray(),
            ],
            'ip' => $request->ip() ?? '127.0.0.1',
            'created_at' => now(),
        ]);

        $redirectUrl = $this->determinarRedireccionPorRol($user);

        // Si el usuario tiene rol cliente: Redirigir a la tienda pública / (public.home)
        // y olvidar cualquier url.intended previa (por ejemplo, si intentó ingresar a /admin)
        if ($user->esCliente() && ! $user->tieneRol('administrador', 'encargado', 'ventas', 'almacen', 'compras')) {
            $request->session()->forget('url.intended');

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Sesión iniciada correctamente.',
                    'user' => [
                        'id' => $user->id,
                        'nombre' => $user->nombre,
                        'email' => $user->email,
                        'roles' => $user->roles->pluck('nombre')->toArray(),
                    ],
                    'redirect' => $redirectUrl,
                ]);
            }

            return redirect()->route('public.home');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Sesión iniciada correctamente.',
                'user' => [
                    'id' => $user->id,
                    'nombre' => $user->nombre,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('nombre')->toArray(),
                ],
                'redirect' => $redirectUrl,
            ]);
        }

        return redirect()->intended($redirectUrl);
    }

    /**
     * Cierra la sesión activa del usuario y destruye la sesión.
     */
    public function logout(Request $request): RedirectResponse|JsonResponse
    {
        $usuario = Auth::user();

        if ($usuario) {
            Bitacora::create([
                'usuario_id' => $usuario->getAuthIdentifier(),
                'accion' => 'LOGOUT',
                'tabla_afectada' => 'usuarios',
                'registro_id' => $usuario->getAuthIdentifier(),
                'detalle' => [
                    'ip' => $request->ip(),
                ],
                'ip' => $request->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Sesión cerrada exitosamente.',
                'redirect' => route('public.home'),
            ]);
        }

        return redirect()->route('public.home')->with('status', 'Has cerrado sesión exitosamente.');
    }

    /**
     * Determina la ruta de destino inicial según el rol del usuario autenticado:
     * - Si el usuario tiene rol cliente: Tienda pública / (public.home).
     * - Si el usuario tiene rol administrador, encargado, ventas, almacen o compras: Panel /admin.
     */
    protected function determinarRedireccionPorRol(Usuario $user): string
    {
        if ($user->tieneRol('administrador', 'encargado', 'ventas', 'almacen', 'compras')) {
            return route('admin.dashboard');
        }

        return route('public.home');
    }

    /**
     * Genera la clave de limitación de tasa basada en el correo electrónico y la IP.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower((string) $request->input('email', '')).'|'.$request->ip());
    }
}
