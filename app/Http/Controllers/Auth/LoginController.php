<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\CheckoutSimuladoController;
use App\Http\Controllers\Controller;
use App\Http\Middleware\IsolateTabSession;
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

        // 4. Limpieza del limitador; si no hay otras pestañas activas, regenerar ID de sesión
        RateLimiter::clear($throttleKey);
        $existingTabs = (array) $request->session()->get(IsolateTabSession::SESSION_MAP_KEY, []);
        if (empty($existingTabs)) {
            $request->session()->regenerate();
        }

        // 5. Vincular y sincronizar carrito de sesión y reservas activas al cliente autenticado
        CheckoutSimuladoController::sincronizarCarritoTrasLogin($user);

        // 6. Leer el tab_token enviado por el formulario (input 'tab_token', header 'X-Tab-Token' o cookie)
        //    y registrarlo de inmediato en el almacenamiento de sesiones activas por pestaña vinculado a Auth::id()
        $incomingTabToken = $request->input('tab_token')
            ?: $request->header('X-Tab-Token')
            ?: IsolateTabSession::resolveTabId($request);

        $tabSessionId = IsolateTabSession::bindAuthenticatedTab(
            $request,
            $user,
            is_string($incomingTabToken) ? trim($incomingTabToken) : null
        );

        Bitacora::create([
            'usuario_id' => $user->id,
            'accion' => 'LOGIN_EXITOSO',
            'tabla_afectada' => 'usuarios',
            'registro_id' => $user->id,
            'detalle' => [
                'email' => $user->email,
                'roles' => $user->roles->pluck('nombre')->toArray(),
                'tab_session_id' => $tabSessionId ?: null,
            ],
            'ip' => $request->ip() ?? '127.0.0.1',
            'created_at' => now(),
        ]);

        $redirectUrl = $this->determinarRedireccionPorRol($user);
        $intended = $request->input('intended') ?? session('url.intended');

        // Si el usuario tiene rol cliente: devolver a la URL pública de origen (url.intended) o checkout sin permitir rutas administrativas
        if ($user->esCliente() && ! $user->tieneRol('administrador', 'encargado', 'ventas', 'almacen', 'compras')) {
            if (is_string($intended) && Str::contains($intended, ['/checkout', 'checkout'])) {
                $redirectUrl = route('checkout.index');
            } elseif (! empty(session('carrito_archivum', [])) && $request->filled('intended') && Str::contains((string) $intended, 'checkout')) {
                $redirectUrl = route('checkout.index');
            } elseif (is_string($intended) && $this->esUrlIntendedPublicaSegura($intended)) {
                $redirectUrl = $intended;
            } else {
                $redirectUrl = route('public.home');
            }

            $request->session()->forget('url.intended');
            $redirectUrl = IsolateTabSession::appendTabQueryToUrl($redirectUrl, $tabSessionId);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Sesión iniciada correctamente.',
                    'tab_token' => $tabSessionId,
                    'tab_session_id' => $tabSessionId,
                    'user' => [
                        'id' => $user->id,
                        'nombre' => $user->nombre,
                        'email' => $user->email,
                        'roles' => $user->roles->pluck('nombre')->toArray(),
                    ],
                    'redirect' => $redirectUrl,
                ]);
            }

            return redirect()->to($redirectUrl);
        }

        // Si el empleado no es Administrador (ej. Almacén, Ventas, Compras), limpiar cualquier url.intended
        // que apunte al Dashboard Financiero (/admin) para redirigirlo directamente a su panel operativo de sede.
        if (! $user->esAdmin()) {
            $request->session()->forget('url.intended');
        } elseif (is_string($intended) && Str::startsWith((string) parse_url($intended, PHP_URL_PATH), '/admin')) {
            $redirectUrl = $intended;
            $request->session()->forget('url.intended');
        }

        $redirectUrl = IsolateTabSession::appendTabQueryToUrl($redirectUrl, $tabSessionId);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Sesión iniciada correctamente.',
                'tab_token' => $tabSessionId,
                'tab_session_id' => $tabSessionId,
                'user' => [
                    'id' => $user->id,
                    'nombre' => $user->nombre,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('nombre')->toArray(),
                ],
                'redirect' => $redirectUrl,
            ]);
        }

        return redirect()->to($redirectUrl);
    }

    /**
     * Cierra la sesión activa de la pestaña actual (sin destruir las sesiones de otras pestañas independientes).
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

        // Desvincular únicamente el tab_token de la pestaña que solicitó cerrar sesión,
        // sin invalidar la sesión compartida ni afectar las demás pestañas abiertas.
        IsolateTabSession::unbindTab($request);

        $guard = Auth::guard('web');
        $request->session()->forget([
            $guard->getName(),
            'carrito_archivum',
            'carrito_reservas_ids',
            'carrito_expira_en',
        ]);
        if (method_exists($guard, 'forgetUser')) {
            $guard->forgetUser();
        }

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
     * Verifica si una URL intended corresponde a una página pública válida para clientes
     * (excluyendo paneles administrativos, login, registro y logout).
     */
    protected function esUrlIntendedPublicaSegura(string $url): bool
    {
        $trimmed = trim($url);
        if ($trimmed === '') {
            return false;
        }

        $path = (string) (parse_url($trimmed, PHP_URL_PATH) ?? '');
        if ($path === '') {
            return false;
        }

        $prefijosBloqueados = ['/admin', '/ventas', '/almacen', '/compras', '/login', '/register', '/logout', '/carrito/'];
        foreach ($prefijosBloqueados as $prefijo) {
            if (Str::startsWith($path, $prefijo)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determina la ruta de destino inicial según el rol del usuario autenticado:
     * - Administrador: Dashboard Financiero General (/admin).
     * - Almacén / Encargado: Panel Operativo de Sede / Inventario (/almacen).
     * - Ventas: Panel Operativo de Ventas, Pedidos y Devoluciones (/ventas).
     * - Compras: Panel Operativo de Compras y Proveedores (/compras).
     * - Cliente: Tienda pública / (public.home).
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

    /**
     * Genera la clave de limitación de tasa basada en el correo electrónico y la IP.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower((string) $request->input('email', '')).'|'.$request->ip());
    }
}
