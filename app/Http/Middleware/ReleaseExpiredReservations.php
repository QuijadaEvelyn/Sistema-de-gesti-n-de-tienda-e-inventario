<?php

namespace App\Http\Middleware;

use App\Services\ReservaService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ReleaseExpiredReservations
{
    /**
     * Inyecta el servicio de gestión de reservas de stock.
     */
    public function __construct(
        protected ReservaService $reservaService,
    ) {}

    /**
     * Antes de procesar solicitudes de checkout o consulta de stock, libera
     * automáticamente las reservas de inventario que hayan expirado.
     *
     * Esto garantiza que las existencias retenidas vencidas queden disponibles
     * para otros clientes antes de evaluar el stock actual.
     *
     * Uso en rutas: ->middleware('reservas.liberar')
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $liberadas = $this->reservaService->liberarReservasVencidas();

            if ($liberadas > 0) {
                Log::info("[ReleaseExpiredReservations] {$liberadas} reserva(s) vencida(s) liberadas antes de procesar la solicitud.", [
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                ]);
            }
        } catch (\Throwable $e) {
            // No interrumpir la solicitud si el proceso de liberación falla;
            // se registra el error para revisión posterior sin bloquear al usuario.
            Log::error('[ReleaseExpiredReservations] Error al liberar reservas vencidas.', [
                'exception' => $e->getMessage(),
                'url' => $request->fullUrl(),
            ]);
        }

        return $next($request);
    }
}
