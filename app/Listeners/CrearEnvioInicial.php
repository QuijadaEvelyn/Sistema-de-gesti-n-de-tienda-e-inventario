<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\PedidoPagado;
use App\Services\EnvioService;
use Illuminate\Support\Facades\Log;
use Throwable;

class CrearEnvioInicial
{
    public function __construct(
        private readonly EnvioService $envioService,
    ) {}

    /**
     * Procesa el evento creando la guia y registro inicial de envio para el pedido.
     */
    public function handle(PedidoPagado $event): void
    {
        $pedido = $event->pedido;

        $pedido->loadMissing('direccionEnvio');

        $direccionTexto = $pedido->direccionEnvio
            ? trim(sprintf(
                '%s %s, %s, CP %s, %s, %s',
                $pedido->direccionEnvio->calle ?? '',
                $pedido->direccionEnvio->numero_exterior ?? '',
                $pedido->direccionEnvio->colonia ?? '',
                $pedido->direccionEnvio->codigo_postal ?? '',
                $pedido->direccionEnvio->ciudad ?? '',
                $pedido->direccionEnvio->estado ?? ''
            ))
            : 'Entrega en sucursal / Domicilio registrado';

        try {
            $envio = $this->envioService->crearEnvio(
                pedido: $pedido,
                direccionEntrega: $direccionTexto,
                paqueteria: 'Pendiente de asignacion',
            );

            Log::info("[Listener] Envio inicial generado con guia {$envio->numero_guia} para pedido {$pedido->folio}.");
        } catch (Throwable $e) {
            Log::warning("[Listener] No se pudo crear el envio inicial para el pedido {$pedido->folio}: {$e->getMessage()}");
        }
    }
}
