<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\PedidoPagado;
use App\Services\NotificacionService;
use Illuminate\Support\Facades\Log;
use Throwable;

class EnviarCorreoConfirmacion
{
    public function __construct(
        private readonly NotificacionService $notificacionService,
    ) {}

    /**
     * Procesa el evento enviando el correo de confirmacion al cliente.
     */
    public function handle(PedidoPagado $event): void
    {
        try {
            $this->notificacionService->enviarConfirmacionPedido($event->pedido);
            Log::info("[Listener] Correo de confirmacion procesado para pedido {$event->pedido->folio}.");
        } catch (Throwable $e) {
            Log::error("[Listener] Error enviando confirmacion para pedido {$event->pedido->folio}: {$e->getMessage()}");
        }
    }
}
