<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Pedido;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Evento disparado cuando un pedido queda confirmado como pagado.
 *
 * Listeners registrados:
 *  - RegistrarSalidaDefinitivaInventario: convierte reservas en movimiento definitivo.
 *  - CrearEnvioInicial: inicializa el registro de envio/paqueteria.
 *  - EnviarCorreoConfirmacion: envia la notificacion al cliente.
 */
class PedidoPagado
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Crea una nueva instancia del evento.
     *
     * @param  Pedido  $pedido  Pedido que acaba de ser pagado/confirmado.
     * @param  bool  $tieneReservas  Indica si el pedido tiene reservas de stock previas.
     */
    public function __construct(
        public readonly Pedido $pedido,
        public readonly bool $tieneReservas = false,
    ) {}
}
