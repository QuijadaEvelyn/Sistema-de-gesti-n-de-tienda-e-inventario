<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\SucursalContextoService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class VerifyBranchAccess
{
    /**
     * Roles que operan bajo contexto de sucursal en almacén o ventas.
     *
     * @var list<string>
     */
    protected array $rolesConSucursal = [
        'almacen',
        'almacenista',
        'encargado',
        'ventas',
        'supervisor',
    ];

    /**
     * Verifica y sincroniza el acceso por sucursal para usuarios con rol de Almacén o Ventas.
     *
     * - Si el modelo Usuario tiene un atributo `sucursal_id` explícitamente asignado, valida que
     *   cualquier `sucursal_id` solicitado coincida con dicha sucursal autorizada.
     * - Si la tabla `usuarios` no posee columna `sucursal_id` (o el usuario opera mediante el selector
     *   de sede en sesión), resuelve y asigna automáticamente la sede activa mediante SucursalContextoService
     *   para permitir la navegación fluida entre Ajustes, Recepciones, Transferencias y Envíos.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $this->responderNoAutenticado($request);
        }

        // Administradores tienen acceso irrestricto a cualquier sucursal
        if (method_exists($user, 'esAdmin') && $user->esAdmin()) {
            return $next($request);
        }

        $requiereSucursal = method_exists($user, 'tieneRol')
            && $user->tieneRol($this->rolesConSucursal);

        if (! $requiereSucursal) {
            return $next($request);
        }

        $tieneColumnaSucursal = Schema::hasTable('usuarios') && Schema::hasColumn('usuarios', 'sucursal_id');
        $sucursalAsignadaExplicita = $user->getAttributes()['sucursal_id'] ?? null;

        // Caso 1: El usuario tiene una sucursal fija asignada explícitamente en su registro
        if ($sucursalAsignadaExplicita !== null) {
            $sucursalSolicitada = $this->resolverSucursalSolicitada($request);

            if ($sucursalSolicitada !== null && (int) $sucursalSolicitada !== (int) $sucursalAsignadaExplicita) {
                return $this->responderForbidden(
                    $request,
                    "Acceso denegado: solo puedes operar en tu sucursal asignada (ID: {$sucursalAsignadaExplicita})."
                );
            }

            $request->merge(['sucursal_id' => (int) $sucursalAsignadaExplicita]);

            return $next($request);
        }

        // Caso 2: Si existe la columna sucursal_id en la tabla usuarios pero el usuario no tiene ninguna asignada
        if ($tieneColumnaSucursal && $sucursalAsignadaExplicita === null) {
            return $this->responderForbidden(
                $request,
                'Tu usuario no tiene una sucursal asignada. Contacta al administrador del sistema.'
            );
        }

        // Caso 3: El sistema gestiona la sede activa mediante SucursalContextoService (sesión / selector de sede)
        $sucursalActiva = app(SucursalContextoService::class)->resolverSucursalAdmin($request, $user);
        if ($sucursalActiva !== null && ! $request->filled('sucursal_id')) {
            $request->merge(['sucursal_id' => $sucursalActiva->id]);
        }

        return $next($request);
    }

    /**
     * Resuelve el sucursal_id que la solicitud intenta utilizar.
     */
    protected function resolverSucursalSolicitada(Request $request): ?int
    {
        $valor = $request->route('sucursal_id')
            ?? $request->input('sucursal_id');

        return is_numeric($valor) ? (int) $valor : null;
    }

    /**
     * Respuesta para usuario no autenticado.
     */
    protected function responderNoAutenticado(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No autenticado. Por favor inicia sesión.',
            ], 401);
        }

        abort(401, 'No autenticado. Por favor inicia sesión.');
    }

    /**
     * Respuesta de acceso denegado (403 Forbidden).
     */
    protected function responderForbidden(Request $request, string $mensaje): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => $mensaje,
            ], 403);
        }

        abort(403, $mensaje);
    }
}
