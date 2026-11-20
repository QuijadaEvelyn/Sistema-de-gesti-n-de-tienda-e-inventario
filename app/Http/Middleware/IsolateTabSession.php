<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Usuario;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsolateTabSession
{
    /**
     * Clave en la sesión donde se almacena el mapa de sesiones autenticadas por pestaña.
     */
    public const SESSION_MAP_KEY = 'archivum_tab_sessions';

    /**
     * Extrae y valida el identificador de pestaña (`X-Tab-Session-Id`) si fue enviado explícitamente.
     */
    public static function resolveTabId(Request $request): ?string
    {
        $raw = $request->header('X-Tab-Session-Id')
            ?: $request->input('tab_session_id');

        if (! is_string($raw)) {
            return null;
        }

        $trimmed = trim($raw);

        if ($trimmed === '' || ! preg_match('/^[A-Za-z0-9_-]{6,80}$/', $trimmed)) {
            return null;
        }

        return $trimmed;
    }

    /**
     * Vincula un usuario recién autenticado con el `tab_session_id` de la pestaña actual si se proporcionó.
     */
    public static function bindAuthenticatedTab(Request $request, Usuario $user, ?string $tabId = null): string
    {
        $resolvedTabId = $tabId ?: self::resolveTabId($request);

        if ($resolvedTabId === null) {
            return '';
        }

        /** @var array<string, array<string, mixed>> $tabSessions */
        $tabSessions = (array) $request->session()->get(self::SESSION_MAP_KEY, []);

        $tabSessions[$resolvedTabId] = [
            'user_id' => (int) $user->getAuthIdentifier(),
            'carrito_archivum' => $request->session()->get('carrito_archivum', []),
            'carrito_reservas_ids' => $request->session()->get('carrito_reservas_ids', []),
            'carrito_expira_en' => $request->session()->get('carrito_expira_en'),
            'authenticated_at' => now()->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
        ];

        $request->session()->put(self::SESSION_MAP_KEY, $tabSessions);
        $request->attributes->set('archivum_resolved_tab_id', $resolvedTabId);

        return $resolvedTabId;
    }

    /**
     * Desvincula únicamente la pestaña actual al cerrar sesión.
     */
    public static function unbindTab(Request $request, ?string $tabId = null): bool
    {
        $resolvedTabId = $tabId
            ?: (string) $request->attributes->get('archivum_resolved_tab_id', '')
            ?: (string) self::resolveTabId($request);

        /** @var array<string, array<string, mixed>> $tabSessions */
        $tabSessions = (array) $request->session()->get(self::SESSION_MAP_KEY, []);

        if ($resolvedTabId !== '' && isset($tabSessions[$resolvedTabId])) {
            unset($tabSessions[$resolvedTabId]);
            $request->session()->put(self::SESSION_MAP_KEY, $tabSessions);

            return ! empty($tabSessions);
        }

        return false;
    }

    /**
     * Si la petición envía explícitamente `X-Tab-Session-Id`, aísla el contexto por ese identificador.
     * En navegaciones normales del navegador (sin cabecera), delega limpiamente al guard de sesión
     * y al control de `sessionStorage` (`archivum_tab_ok` / `archivum_tab_logout_done`) en los layouts.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->hasSession()) {
            return $next($request);
        }

        $headerTabId = $request->header('X-Tab-Session-Id');
        if (! is_string($headerTabId) || trim($headerTabId) === '') {
            return $next($request);
        }

        $tabId = self::resolveTabId($request);
        if ($tabId === null) {
            return $next($request);
        }

        /** @var array<string, array<string, mixed>> $tabSessions */
        $tabSessions = (array) $request->session()->get(self::SESSION_MAP_KEY, []);
        $guard = Auth::guard('web');
        $guardSessionKey = $guard->getName();

        if (isset($tabSessions[$tabId]['user_id'])) {
            $userId = (int) $tabSessions[$tabId]['user_id'];
            /** @var Usuario|null $user */
            $user = Usuario::find($userId);

            if ($user !== null && $user->activo) {
                $request->session()->put($guardSessionKey, $user->getAuthIdentifier());
                $guard->setUser($user);
                $request->setUserResolver(fn (): Usuario => $user);
                $request->attributes->set('archivum_resolved_tab_id', $tabId);
            }
        } elseif (! empty($tabSessions)) {
            $request->session()->forget([
                $guardSessionKey,
                'carrito_archivum',
                'carrito_reservas_ids',
                'carrito_expira_en',
            ]);

            if (method_exists($guard, 'forgetUser')) {
                $guard->forgetUser();
            }

            $request->setUserResolver(fn (): null => null);
        }

        return $next($request);
    }
}
