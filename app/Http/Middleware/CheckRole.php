<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Mapeo de sinónimos y alias comunes para roles del sistema.
     *
     * @var array<string, list<string>>
     */
    protected array $aliasMap = [
        'admin' => ['admin', 'administrador'],
        'administrador' => ['admin', 'administrador'],
        'encargado' => ['encargado', 'supervisor', 'almacenista', 'gerente'],
        'almacenista' => ['almacenista', 'encargado'],
        'cliente' => ['cliente', 'comprador', 'consumidor'],
    ];

    /**
     * Maneja la petición entrante y valida el rol del usuario autenticado.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string  ...$roles  Uno o más roles permitidos (ej. 'administrador', 'encargado')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // 1. Validar que el usuario esté autenticado
        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No autenticado. Por favor inicia sesión.',
                ], 401);
            }

            return Route::has('login')
                ? redirect()->guest(route('login'))
                : abort(401, 'No autenticado. Por favor inicia sesión.');
        }

        // 2. Validar estado de la cuenta (campo 'activo' en tabla usuarios)
        if (isset($user->activo) && ! $user->activo) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tu cuenta de usuario se encuentra inactiva.',
                ], 403);
            }

            abort(403, 'Tu cuenta de usuario se encuentra inactiva. Contacta al administrador.');
        }

        // Si no se pasaron roles al middleware, solo requería estar autenticado y activo
        if (empty($roles)) {
            return $next($request);
        }

        // 3. Normalizar los roles permitidos recibidos (soporta 'rol1,rol2' o 'rol1|rol2')
        $rolesSolicitados = collect($roles)
            ->flatMap(function (string $rol): array {
                return preg_split('/[,|]/', $rol) ?: [$rol];
            })
            ->map(fn (string $rol): string => mb_strtolower(trim($rol)))
            ->filter()
            ->values()
            ->all();

        // Expandir con posibles alias (por ejemplo 'admin' equivale a 'administrador')
        $rolesPermitidos = collect($rolesSolicitados)
            ->flatMap(fn (string $r): array => $this->aliasMap[$r] ?? [$r])
            ->unique()
            ->all();

        // 4. Obtener los nombres de los roles del usuario autenticado
        $tienePermiso = false;

        if (method_exists($user, 'tieneRol')) {
            $tienePermiso = $user->tieneRol($rolesPermitidos);
        } elseif (method_exists($user, 'roles')) {
            $rolesUsuario = ($user->relationLoaded('roles') ? $user->roles : $user->roles()->get())
                ->pluck('nombre')
                ->map(fn (mixed $r): string => mb_strtolower(trim((string) $r)))
                ->all();

            $tienePermiso = ! empty(array_intersect($rolesPermitidos, $rolesUsuario));
        }

        // 5. Si no cuenta con el rol requerido, denegar acceso
        if (! $tienePermiso) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Acceso denegado: no cuentas con los permisos requeridos.',
                    'roles_requeridos' => $rolesSolicitados,
                ], 403);
            }

            abort(
                403,
                'Acceso denegado: Esta acción requiere uno de los siguientes roles: '.implode(', ', $rolesSolicitados).'.'
            );
        }

        return $next($request);
    }
}
