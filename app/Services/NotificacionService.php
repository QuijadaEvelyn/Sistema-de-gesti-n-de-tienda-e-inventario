<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Envio;
use App\Models\Pedido;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NotificacionService
{
    /**
     * Envia la notificacion y correo de confirmacion de pedido al cliente.
     */
    public function enviarConfirmacionPedido(Pedido $pedido): void
    {
        $pedido->loadMissing(['cliente', 'detalles.varianteSku.producto']);

        $email = $pedido->cliente?->email;
        $nombreCliente = trim(($pedido->cliente?->nombre ?? '').' '.($pedido->cliente?->apellidos ?? ''));

        Log::info('[NotificacionService] Confirmacion de pedido enviada.', [
            'pedido_id' => $pedido->id,
            'folio' => $pedido->folio,
            'cliente' => $nombreCliente,
            'email' => $email,
            'total' => $pedido->total,
            'items_count' => $pedido->detalles->count(),
        ]);

        // Nota: En un entorno de produccion con Mailables configurados:
        // if (! empty($email)) {
        //     Mail::to($email)->queue(new ConfirmacionPedidoMailable($pedido));
        // }
    }

    /**
     * Envia la notificacion con los datos y codigo de barras/numerico de la ficha OXXO.
     */
    public function enviarFichaOxxo(Pedido $pedido, string $codigoOxxo, Carbon $expiraEn): void
    {
        $pedido->loadMissing('cliente');

        $email = $pedido->cliente?->email;

        Log::info('[NotificacionService] Ficha de pago OXXO enviada al cliente.', [
            'pedido_id' => $pedido->id,
            'folio' => $pedido->folio,
            'email' => $email,
            'codigo_oxxo' => $codigoOxxo,
            'expira_en' => $expiraEn->toDateTimeString(),
            'monto' => $pedido->total,
        ]);

        // if (! empty($email)) {
        //     Mail::to($email)->queue(new FichaOxxoMailable($pedido, $codigoOxxo, $expiraEn));
        // }
    }

    /**
     * Envia actualizacion de rastreo y estado del paquete al destinatario.
     */
    public function enviarActualizacionRastreo(
        Envio $envio,
        string $estado,
        string $ubicacion,
        string $comentario,
    ): void {
        $envio->loadMissing('pedido.cliente');

        $email = $envio->pedido?->cliente?->email;

        Log::info('[NotificacionService] Notificacion de rastreo enviada al cliente.', [
            'envio_id' => $envio->id,
            'numero_guia' => $envio->numero_guia,
            'pedido_folio' => $envio->pedido?->folio,
            'email' => $email,
            'nuevo_estado' => $estado,
            'ubicacion' => $ubicacion,
            'comentario' => $comentario,
        ]);

        // if (! empty($email)) {
        //     Mail::to($email)->queue(new ActualizacionRastreoMailable($envio, $estado, $ubicacion, $comentario));
        // }
    }

    /**
     * Envia notificacion de resolucion de devolucion.
     */
    public function enviarNotificacionDevolucion(Pedido $pedido, float $montoReembolso, string $motivo): void
    {
        $pedido->loadMissing('cliente');

        Log::info('[NotificacionService] Notificacion de devolucion emitida.', [
            'pedido_id' => $pedido->id,
            'folio' => $pedido->folio,
            'email' => $pedido->cliente?->email,
            'monto_reembolso' => $montoReembolso,
            'motivo' => $motivo,
        ]);
    }
}
