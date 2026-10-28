<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyBranchAccess
{
    /**
     * Roles que requieren validación de sucursal asignada.
     *
     * @var list<string>
     */
    protected array $rolesConSucursal = [
        'almacenista',
        'encargado',
        'ventas',
        'supervisor',
    ];

    /**
     * Verifica que los usuarios con rol de Almacén o Ventas tengan una sucursal asignada
     * (campo sucursal_id en el modelo Usuario) y que las operaciones correspondan
     * únicamente a su sucursal autorizada.
     *
     * El parámetro de sucursal puede venir como:
     *  - Parámetro de ruta:     {sucursal_id}
     *  - Query string:          ?sucursal_id=1
     *  - Body JSON/form:        sucursal_id=1
     *
     * Uso en rutas: ->middleware('sucursal.acceso')
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

        // Verificar si el usuario posee alguno de los roles que requieren sucursal
        $requiereSucursal = false;

        if (method_exists($user, 'tieneRol')) {
            $requiereSucursal = $user->tieneRol($this->rolesConSucursal);
        }

        if (! $requiereSucursal) {
            // Usuarios sin roles de almacén/ventas no están sujetos a esta restricción
            return $next($request);
        }

        // 1. Verificar que el usuario tenga una sucursal asignada
        $sucursalAsignada = $user->sucursal_id ?? null;

        if (! $sucursalAsignada) {
            return $this->responderForbidden(
                $request,
                'Tu usuario no tiene una sucursal asignada. Contacta al administrador del sistema.'
            );
        }

        // 2. Si la solicitud incluye un sucursal_id, validar que coincida con la sucursal autorizada
        $sucursalSolicitada = $this->resolverSucursalSolicitada($request);

        if ($sucursalSolicitada !== null && (int) $sucursalSolicitada !== (int) $sucursalAsignada) {
            return $this->responderForbidden(
                $request,
                "Acceso denegado: solo puedes operar en tu sucursal asignada (ID: {$sucursalAsignada})."
            );
        }

        // 3. Inyectar la sucursal autorizada en el request para que los controladores la lean
        $request->merge(['sucursal_id' => (int) $sucursalAsignada]);

        return $next($request);
    }

    /**
     * Resuelve el sucursal_id que la solicitud intenta utilizar.
     * Prioridad: parámetro de ruta > body > query string.
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
