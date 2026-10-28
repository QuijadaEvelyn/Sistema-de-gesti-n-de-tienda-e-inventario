<?php

use App\Models\Bitacora;
use App\Models\Pago;
use App\Services\ReservaService;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:limpiar-reservas-y-pedidos-expirados', function (ReservaService $reservaService) {
    $this->info('Iniciando liberacion de reservas vencidas y cancelacion de pedidos con ficha expirada...');

    // 1. Liberar reservas de stock vencidas
    $liberadas = $reservaService->liberarReservasVencidas();
    $this->info("Reservas vencidas liberadas: {$liberadas}");

    // 2. Cancelar pedidos pendientes con ficha de pago expirada
    $pagosExpirados = Pago::query()
        ->where('metodo', 'efectivo')
        ->where('estatus', 'pendiente')
        ->whereHas('pedido', function ($q): void {
            $q->where('estatus', 'pendiente');
        })
        ->with(['detalles', 'pedido.reservasStock'])
        ->get();

    $cancelados = 0;

    foreach ($pagosExpirados as $pago) {
        $detalleOxxo = $pago->detalles->first();
        $expiraEn = null;

        if ($detalleOxxo && isset($detalleOxxo->respuesta_pasarela['expira_en'])) {
            $expiraEn = Carbon::parse($detalleOxxo->respuesta_pasarela['expira_en']);
        } else {
            $expiraEn = $pago->created_at ? $pago->created_at->addDays(3) : Carbon::now()->subMinute();
        }

        if (Carbon::now()->gt($expiraEn)) {
            DB::transaction(function () use ($pago, &$cancelados): void {
                $pago->update(['estatus' => 'rechazado']);

                if ($pago->pedido && $pago->pedido->estatus === 'pendiente') {
                    $pago->pedido->update(['estatus' => 'cancelado']);

                    // Revertir existencias de reservas activas vinculadas al pedido
                    foreach ($pago->pedido->reservasStock()->where('estado', 'activa')->get() as $reserva) {
                        if ($reserva->existencia) {
                            $reserva->existencia->increment('cantidad', $reserva->cantidad);
                        }
                        $reserva->update(['estado' => 'expirada']);
                    }

                    Bitacora::create([
                        'usuario_id' => null,
                        'accion' => 'PEDIDO_CANCELADO_FICHA_EXPIRADA',
                        'tabla_afectada' => 'pedidos',
                        'registro_id' => $pago->pedido->id,
                        'detalle' => [
                            'folio' => $pago->pedido->folio,
                            'pago_id' => $pago->id,
                            'metodo' => $pago->metodo,
                            'motivo' => 'Ficha de pago OXXO vencida sin confirmar',
                        ],
                        'ip' => '127.0.0.1',
                        'created_at' => now(),
                    ]);

                    $cancelados++;
                }
            });
        }
    }

    $this->info("Pedidos con ficha expirada cancelados: {$cancelados}");
    Log::info("[Scheduler] Limpieza ejecutada: {$liberadas} reservas liberadas, {$cancelados} pedidos cancelados.");
})->purpose('Libera reservas vencidas y cancela pedidos pendientes con ficha de pago expirada');

// Programador de Tareas cada 5 minutos (everyFiveMinutes)
Schedule::command('app:limpiar-reservas-y-pedidos-expirados')->everyFiveMinutes();
