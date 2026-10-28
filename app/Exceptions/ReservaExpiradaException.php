<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Excepcion lanzada cuando se intenta operar sobre una reserva de stock
 * que ya ha expirado o fue liberada/expirada antes de la operacion actual.
 *
 * Codigo HTTP: 409 Conflict — la reserva existio pero ya no es valida.
 */
class ReservaExpiradaException extends Exception
{
    /**
     * Crea una nueva instancia de la excepcion de reserva expirada.
     *
     * @param  string  $message  Mensaje descriptivo del error.
     * @param  int|null  $reservaId  ID de la reserva que expiro.
     * @param  int|null  $pedidoId  ID del pedido afectado por la expiracion.
     * @param  Exception|null  $previous  Excepcion anterior encadenada.
     */
    public function __construct(
        string $message = 'La reserva de stock ha expirado. Por favor, inicie el proceso de compra nuevamente.',
        int $code = 409,
        ?Exception $previous = null,
        public readonly ?int $reservaId = null,
        public readonly ?int $pedidoId = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Renderiza la excepcion como respuesta JSON para peticiones API.
     *
     * Devuelve HTTP 409 Conflict con detalle de la reserva afectada.
     */
    public function render(Request $request): JsonResponse
    {
        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'error' => 'RESERVA_EXPIRADA',
                'message' => $this->getMessage(),
                'detalle' => [
                    'reserva_id' => $this->reservaId,
                    'pedido_id' => $this->pedidoId,
                ],
            ], 409);
        }

        // Para peticiones web, dejar que el handler global lo maneje.
        return response()->json(['message' => $this->getMessage()], 409);
    }
}
