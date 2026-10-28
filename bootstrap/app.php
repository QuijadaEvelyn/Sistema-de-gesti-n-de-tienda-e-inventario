<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\EnsureClientOwnership;
use App\Http\Middleware\ReleaseExpiredReservations;
use App\Http\Middleware\VerifyBranchAccess;
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
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
