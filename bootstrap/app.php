<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\EnsureClientOwnership;
use App\Http\Middleware\IsolateTabSession;
use App\Http\Middleware\ReleaseExpiredReservations;
use App\Http\Middleware\VerifyBranchAccess;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Aislamiento estricto de sesión por pestaña del navegador (sessionStorage + tab_session_id)
        $middleware->web(append: [
            IsolateTabSession::class,
        ]);

        // Redirección condicional para usuarios con sesión activa que intenten acceder a /login o /register
        $middleware->redirectUsersTo(function (Request $request): string {
            $user = $request->user();
            if (! $user) {
                return route('public.home');
            }

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
        });

        $middleware->alias([
            // Control de acceso por rol
            'rol' => CheckRole::class,
            'role' => CheckRole::class,
            // Verifica que el recurso (Pedido, Carrito, Dirección) pertenezca al cliente autenticado
            'cliente.propietario' => EnsureClientOwnership::class,
            // Restringe a usuarios Almacén/Ventas a operar solo en su sucursal asignada
            'sucursal.acceso' => VerifyBranchAccess::class,
            // Libera reservas de stock vencidas antes de procesar checkout o consultas de stock
            'reservas.liberar' => ReleaseExpiredReservations::class,
            // Aislamiento de sesión por pestaña
            'tab.session' => IsolateTabSession::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson() || $request->ajax() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'unauthenticated',
                    'message' => 'Inicia sesión o regístrate para reservar piezas en la bóveda.',
                    'login_url' => route('login'),
                    'redirect' => route('login'),
                ], 401);
            }

            $intendedUrl = $request->isMethod('GET')
                ? $request->fullUrl()
                : (url()->previous() ?: route('public.home'));

            session(['url.intended' => $intendedUrl]);

            return redirect()->to(route('login'))
                ->with('warning', 'Inicia sesión o regístrate para reservar piezas en la bóveda.');
        });
    })->create();
