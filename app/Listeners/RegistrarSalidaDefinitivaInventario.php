<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\PedidoPagado;
use App\Models\MovimientoInventario;
use App\Models\Pedido;
use App\Services\InventarioService;
use App\Services\ReservaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegistrarSalidaDefinitivaInventario
{
    public function __construct(
        private readonly ReservaService $reservaService,
        private readonly InventarioService $inventarioService,
    ) {}

    /**
     * Procesa el evento registrando la salida contable de inventario.
     */
    public function handle(PedidoPagado $event): void
    {
        $pedido = $event->pedido;

        // Si el pedido tiene reservas registradas, convertirlas en salida definitiva
        $tieneReservas = $pedido->reservasStock()->whereIn('estado', ['activa', 'confirmada'])->exists();

        if ($tieneReservas) {
            // Asegurar que las reservas esten en estado 'confirmada' antes de convertirlas
            $pedido->reservasStock()->where('estado', 'activa')->update(['estado' => 'confirmada']);
            $this->reservaService->convertirReservaEnSalidaDefinitiva($pedido);

            return;
        }

        // Si no tenia reservas previas pero ya se registraron movimientos de salida para este pedido, evitar duplicar
        $movimientoExistente = MovimientoInventario::query()
            ->where('referencia_type', Pedido::class)
            ->where('referencia_id', $pedido->id)
            ->where('tipo', 'salida')
            ->exists();

        if ($movimientoExistente) {
            return;
        }

        // Si no existian movimientos previos de salida, descontar existencias
        DB::transaction(function () use ($pedido): void {
            $detalles = $pedido->detalles()->with('varianteSku')->get();
            $sucursal = $this->inventarioService->resolverSucursal();

            foreach ($detalles as $detalle) {
                $this->inventarioService->descontarStock(
                    variante: $detalle->variante_sku_id,
                    cantidad: $detalle->cantidad,
                    sucursalId: $sucursal->id,
                    usuario: null,
                    referencia: $pedido,
                    motivo: "Salida definitiva por pago de pedido {$pedido->folio}",
                );
            }
        });

        Log::info("[Listener] Salida definitiva registrada para pedido {$pedido->folio}.");
    }
}
