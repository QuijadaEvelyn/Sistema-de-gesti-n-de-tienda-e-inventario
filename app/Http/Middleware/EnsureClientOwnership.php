<?php

namespace App\Http\Middleware;

use App\Models\Carrito;
use App\Models\Direccion;
use App\Models\Pedido;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClientOwnership
{
    /**
     * Modelos soportados junto con el parámetro de ruta esperado y la FK de cliente.
     *
     * @var array<string, array{model: class-string<Model>, routeParam: string, foreignKey: string}>
     */
    protected array $recursos = [
        'pedido' => ['model' => Pedido::class,   'routeParam' => 'pedido',   'foreignKey' => 'cliente_id'],
        'carrito' => ['model' => Carrito::class,  'routeParam' => 'carrito',  'foreignKey' => 'cliente_id'],
        'direccion' => ['model' => Direccion::class, 'routeParam' => 'direccion', 'foreignKey' => 'cliente_id'],
    ];

    /**
     * Verifica que el recurso solicitado (Pedido, Carrito, Dirección) pertenezca
     * al cliente del usuario autenticado. Retorna 403 si no coincide.
     *
     * Uso en rutas: ->middleware('cliente.propietario:pedido')
     *               ->middleware('cliente.propietario:carrito')
     *               ->middleware('cliente.propietario:direccion')
     *
     * @param  Closure(Request): Response  $next
     * @param  string  $recurso  Tipo de recurso a verificar ('pedido', 'carrito', 'direccion').
     */
    public function handle(Request $request, Closure $next, string $recurso = 'pedido'): Response
    {
        $user = $request->user();

        if (! $user) {
            return $this->responderNoAutenticado($request);
        }

        // Los administradores tienen acceso sin restricción de propiedad
        if (method_exists($user, 'esAdmin') && $user->esAdmin()) {
            return $next($request);
        }

        $recursoKey = mb_strtolower(trim($recurso));

        if (! isset($this->recursos[$recursoKey])) {
            return $next($request);
        }

        $config = $this->recursos[$recursoKey];

        // Obtener el modelo de la ruta (Route Model Binding) o por ID en el segmento
        $modelo = $request->route($config['routeParam']);

        if (! $modelo instanceof Model) {
            // Intentar resolver por ID si el binding no entregó el modelo completo
            $id = $request->route($config['routeParam']);

            if (! is_numeric($id)) {
                return $next($request);
            }

            /** @var Model|null $modelo */
            $modelo = ($config['model'])::query()->find((int) $id);
        }

        if (! $modelo) {
            return $this->responderForbidden($request, 'El recurso solicitado no fue encontrado.');
        }

        // Obtener el cliente_id del usuario autenticado
        $clienteDelUsuario = $user->cliente?->id;

        if (! $clienteDelUsuario) {
            return $this->responderForbidden(
                $request,
                'Tu cuenta no tiene un perfil de cliente asociado para acceder a este recurso.'
            );
        }

        $foreignKey = $config['foreignKey'];

        if ((int) $modelo->{$foreignKey} !== (int) $clienteDelUsuario) {
            return $this->responderForbidden(
                $request,
                'Acceso denegado: este recurso no te pertenece.'
            );
        }

        return $next($request);
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
