<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockInsuficienteException extends Exception
{
    /**
     * Crea una nueva instancia de la excepcion para stock insuficiente.
     */
    public function __construct(
        string $message = 'No hay suficiente stock disponible para completar la operacion.',
        int $code = 422,
        ?Exception $previous = null,
        public readonly ?int $varianteSkuId = null,
        public readonly ?int $solicitado = null,
        public readonly ?int $disponible = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Renderiza la excepcion como respuesta JSON para peticiones API o HTTP.
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'error' => 'STOCK_INSUFICIENTE',
            'message' => $this->getMessage(),
            'detalle' => [
                'variante_sku_id' => $this->varianteSkuId,
                'solicitado' => $this->solicitado,
                'disponible' => $this->disponible,
            ],
        ], 422);
    }
}
