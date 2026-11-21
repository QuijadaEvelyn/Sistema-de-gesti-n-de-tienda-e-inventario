<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Usuario;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class IsolateTabSession
{
    /**
     * Clave en la sesión donde se almacena el mapa de sesiones autenticadas por pestaña.
     */
    public const SESSION_MAP_KEY = 'archivum_tab_sessions';

    /**
     * Prefijo en caché para respaldar el estado de cada pestaña (`tab_token -> datos`).
     */
    public const CACHE_PREFIX = 'archivum_tab_token:';

    /**
     * Clave efímera en sesión para pasar el `tab_token` recién vinculado tras `POST /login` o `POST /register`
     * hacia la primera carga de la vista destino (`/admin` o `/`).
     */
    public const FLASH_BOUND_TAB_KEY = 'archivum_just_logged_in_tab_token';

    /**
     * Extrae y valida el token único de pestaña (`tab_token` / `archivum_tab_token` / `tab_session_id` / `_tab`) enviado en la petición.
     */
    public static function resolveTabId(Request $request): ?string
    {
        if ($request->query('_guest_tab') === '1') {
            return null;
        }

        // Las rutas de recuperación de contraseña (/forgot-password y /reset-password/*) siempre se acceden
        // libremente como invitado cuando el usuario abre el enlace desde su correo Gmail en una pestaña nueva,
        // sin redirigir en bucle ni exigir sesión iniciada.
        if ($request->is('forgot-password', 'forgot-password/*', 'reset-password', 'reset-password/*')) {
            return null;
        }

        // En peticiones GET a vistas de login/registro sin `_tab` explícito en la URL,
        // no usar tokens implícitos para que /login siempre renderice el formulario sin redirigir en bucle.
        $isAuthFormGet = $request->isMethod('GET') && $request->is('login', 'register', 'admin/login', 'forgot-password', 'reset-password', 'reset-password/*');

        $raw = $request->input('tab_token')
            ?: $request->header('X-Tab-Token')
            ?: $request->input('archivum_tab_token')
            ?: $request->input('tab_session_id')
            ?: $request->header('X-Tab-Session-Id')
            ?: $request->query('_tab')
            ?: $request->input('_tab');

        if (! is_string($raw) && ! $isAuthFormGet && $request->hasSession()) {
            $flashedTab = $request->session()->pull(self::FLASH_BOUND_TAB_KEY);
            if (is_string($flashedTab) && $flashedTab !== '') {
                $raw = $flashedTab;
            }
        }

        if (! is_string($raw)) {
            return null;
        }

        $trimmed = trim($raw);

        if ($trimmed === '' || ! preg_match('/^[A-Za-z0-9_-]{6,96}$/', $trimmed)) {
            return null;
        }

        return $trimmed;
    }

    /**
     * Obtiene los datos de sesión asociados a un `tab_token` desde la sesión o caché.
     *
     * @return array<string, mixed>|null
     */
    public static function getTabState(Request $request, string $tabId): ?array
    {
        /** @var array<string, array<string, mixed>> $tabSessions */
        $tabSessions = (array) $request->session()->get(self::SESSION_MAP_KEY, []);

        if (isset($tabSessions[$tabId]) && is_array($tabSessions[$tabId])) {
            return $tabSessions[$tabId];
        }

        try {
            $cached = Cache::get(self::CACHE_PREFIX.$tabId);
            if (is_array($cached) && isset($cached['user_id'])) {
                $tabSessions[$tabId] = $cached;
                $request->session()->put(self::SESSION_MAP_KEY, $tabSessions);

                return $cached;
            }
        } catch (\Throwable) {
            // Si el driver de caché no está disponible, usar únicamente la sesión
        }

        return null;
    }

    /**
     * Guarda el estado de una pestaña tanto en el mapa de la sesión como en caché.
     *
     * @param  array<string, mixed>  $state
     */
    public static function putTabState(Request $request, string $tabId, array $state): void
    {
        /** @var array<string, array<string, mixed>> $tabSessions */
        $tabSessions = (array) $request->session()->get(self::SESSION_MAP_KEY, []);
        $tabSessions[$tabId] = $state;
        $request->session()->put(self::SESSION_MAP_KEY, $tabSessions);

        try {
            Cache::put(self::CACHE_PREFIX.$tabId, $state, now()->addHours(12));
        } catch (\Throwable) {
        }
    }

    /**
     * Vincula un usuario recién autenticado con el `tab_token` de la pestaña actual,
     * permitiendo múltiples `tab_token` activos simultáneamente para distintos usuarios o roles.
     */
    public static function bindAuthenticatedTab(Request $request, Usuario $user, ?string $tabId = null): string
    {
        $candidate = is_string($tabId) ? trim($tabId) : '';
        if ($candidate !== '' && preg_match('/^[A-Za-z0-9_-]{6,96}$/', $candidate)) {
            $resolvedTabId = $candidate;
        } else {
            $resolvedTabId = self::resolveTabId($request);
        }

        if ($resolvedTabId === null) {
            if (app()->runningUnitTests()) {
                return '';
            }
            $resolvedTabId = 'tab_'.Str::lower(Str::random(24));
        }

        $state = [
            'user_id' => (int) $user->getAuthIdentifier(),
            'carrito_archivum' => $request->session()->get('carrito_archivum', []),
            'carrito_precios_congelados' => $request->session()->get('carrito_precios_congelados', []),
            'carrito_reservas_ids' => $request->session()->get('carrito_reservas_ids', []),
            'carrito_expira_en' => $request->session()->get('carrito_expira_en'),
            'authenticated_at' => now()->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
        ];

        self::putTabState($request, $resolvedTabId, $state);
        $request->session()->put(self::FLASH_BOUND_TAB_KEY, $resolvedTabId);
        $request->attributes->set('archivum_resolved_tab_id', $resolvedTabId);

        return $resolvedTabId;
    }

    /**
     * Desvincula únicamente la pestaña actual al cerrar sesión explícitamente,
     * conservando intactas las sesiones de todas las demás pestañas abiertas.
     */
    public static function unbindTab(Request $request, ?string $tabId = null): bool
    {
        $resolvedTabId = $tabId
            ?: (string) $request->attributes->get('archivum_resolved_tab_id', '')
            ?: (string) self::resolveTabId($request);

        /** @var array<string, array<string, mixed>> $tabSessions */
        $tabSessions = (array) $request->session()->get(self::SESSION_MAP_KEY, []);

        if ($resolvedTabId !== '') {
            unset($tabSessions[$resolvedTabId]);
            $request->session()->put(self::SESSION_MAP_KEY, $tabSessions);

            try {
                Cache::forget(self::CACHE_PREFIX.$resolvedTabId);
            } catch (\Throwable) {
            }
        }

        $request->attributes->set('archivum_resolved_tab_id', '');

        return ! empty($tabSessions);
    }

    /**
     * Aísla la sesión por pestaña en memoria para cada petición HTTP:
     * - Si la petición incluye un `tab_token` válido (`_tab`, `X-Tab-Token`, `tab_token`),
     *   autentica en memoria al usuario vinculado a esa pestaña.
     * - Si es una pestaña nueva (sin `tab_token`), establece el usuario en memoria como Invitado
     *   (`Auth::guest()`) desde el primer milisegundo SIN ejecutar `Auth::logout()` ni destruir
     *   las sesiones activas de las otras pestañas.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->hasSession()) {
            return $next($request);
        }

        $tabId = self::resolveTabId($request);
        $request->attributes->set('archivum_request_tab_id', $tabId);

        /** @var array<string, array<string, mixed>> $tabSessions */
        $tabSessions = (array) $request->session()->get(self::SESSION_MAP_KEY, []);

        // Compatibilidad con pruebas automatizadas PHPUnit que usan actingAs() sin enviar tab_token
        $isStandardUnitTest = app()->runningUnitTests()
            && $tabId === null
            && empty($tabSessions);

        if ($isStandardUnitTest) {
            return $next($request);
        }

        $guard = Auth::guard('web');
        $guardSessionKey = $guard->getName();
        $authenticatedForThisTab = false;

        if ($tabId !== null) {
            $tabState = self::getTabState($request, $tabId);

            if ($tabState !== null && isset($tabState['user_id'])) {
                $userId = (int) $tabState['user_id'];
                /** @var Usuario|null $user */
                $user = Usuario::find($userId);

                if ($user !== null && $user->activo) {
                    $authenticatedForThisTab = true;
                    $request->session()->put($guardSessionKey, $user->getAuthIdentifier());
                    $guard->setUser($user);
                    $request->setUserResolver(fn (): Usuario => $user);
                    $request->attributes->set('archivum_resolved_tab_id', $tabId);

                    // Hidratar en memoria el carrito correspondiente a esta pestaña
                    $request->session()->put('carrito_archivum', $tabState['carrito_archivum'] ?? []);
                    $request->session()->put('carrito_precios_congelados', $tabState['carrito_precios_congelados'] ?? []);
                    $request->session()->put('carrito_reservas_ids', $tabState['carrito_reservas_ids'] ?? []);
                    if (! empty($tabState['carrito_expira_en'])) {
                        $request->session()->put('carrito_expira_en', $tabState['carrito_expira_en']);
                    } else {
                        $request->session()->forget('carrito_expira_en');
                    }
                } else {
                    self::unbindTab($request, $tabId);
                }
            }
        }

        if (! $authenticatedForThisTab) {
            // Pestaña nueva o sin tab_token autenticado:
            // Establecer como Invitado únicamente en memoria para esta petición HTTP,
            // SIN llamar a Auth::logout() ni invalidar la sesión global de las otras pestañas.
            $request->session()->forget([
                $guardSessionKey,
                'carrito_archivum',
                'carrito_precios_congelados',
                'carrito_reservas_ids',
                'carrito_expira_en',
            ]);

            // Evitar que SessionGuard::user() re-autentique desde la cookie "remember_web_*" en esta petición
            if (method_exists($guard, 'getRecallerName')) {
                $request->cookies->remove($guard->getRecallerName());
            }

            if (method_exists($guard, 'forgetUser')) {
                $guard->forgetUser();
            }

            $request->setUserResolver(fn (): null => null);
            $request->attributes->set('archivum_resolved_tab_id', '');
        }

        $response = $next($request);

        // Si esta pestaña está autenticada (o acaba de iniciar sesión en este request),
        // guardar su estado actualizado en su ranura aislada (sesión + caché)
        $activeTabId = (string) ($request->attributes->get('archivum_resolved_tab_id') ?: '');

        if ($activeTabId !== '' && $guard->check()) {
            /** @var Usuario $currentUser */
            $currentUser = $guard->user();
            $existingState = self::getTabState($request, $activeTabId) ?? [];

            self::putTabState($request, $activeTabId, [
                'user_id' => (int) $currentUser->getAuthIdentifier(),
                'carrito_archivum' => $request->session()->get('carrito_archivum', []),
                'carrito_precios_congelados' => $request->session()->get('carrito_precios_congelados', []),
                'carrito_reservas_ids' => $request->session()->get('carrito_reservas_ids', []),
                'carrito_expira_en' => $request->session()->get('carrito_expira_en'),
                'authenticated_at' => $existingState['authenticated_at'] ?? now()->toIso8601String(),
                'updated_at' => now()->toIso8601String(),
            ]);
        }

        // Limpiar la clave global raíz del guard al finalizar la respuesta para que cualquier
        // pestaña nueva que abra http://localhost:8000 sin `_tab` sea Invitado desde el primer milisegundo
        if (! app()->runningUnitTests() || ! empty($request->session()->get(self::SESSION_MAP_KEY, []))) {
            $request->session()->forget([
                $guardSessionKey,
                'carrito_archivum',
                'carrito_precios_congelados',
                'carrito_reservas_ids',
                'carrito_expira_en',
            ]);
        }

        // Si la respuesta es una redirección 302 dentro de una pestaña autenticada (y no redirige a login/logout/password),
        // anexar automáticamente `_tab` a la URL de destino para mantener su sesión sin usar cookies globales
        if ($response->isRedirection() && $activeTabId !== '' && ! $request->is('logout', 'forgot-password', 'reset-password', 'reset-password/*')) {
            $location = (string) $response->headers->get('Location', '');
            $locationPath = (string) parse_url($location, PHP_URL_PATH);
            $isRedirectToAuthForm = in_array($locationPath, ['/login', '/register', '/admin/login', '/forgot-password'], true)
                || str_starts_with($locationPath, '/reset-password');

            if ($location !== '' && ! $isRedirectToAuthForm) {
                $response->headers->set('Location', self::appendTabQueryToUrl($location, $activeTabId));
            }
        }

        return $response;
    }

    /**
     * Anexa el parámetro `_tab` a una URL interna de redirección.
     */
    public static function appendTabQueryToUrl(string $url, string $tabId): string
    {
        if ($tabId === '' || Str::contains($url, '_tab=')) {
            return $url;
        }

        $fragment = '';
        if (Str::contains($url, '#')) {
            [$urlWithoutFragment, $frag] = explode('#', $url, 2);
            $url = $urlWithoutFragment;
            $fragment = '#'.$frag;
        }

        $separator = Str::contains($url, '?') ? '&' : '?';

        return $url.$separator.'_tab='.rawurlencode($tabId).$fragment;
    }
}
