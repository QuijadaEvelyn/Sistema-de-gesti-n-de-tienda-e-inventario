<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Existencia;
use App\Models\MovimientoInventario;
use App\Models\Pedido;
use App\Models\PiezaUnica;
use App\Models\ReservaStock;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ReservaService
{
    /**
     * Duración predeterminada (en minutos) de una reserva temporal de carrito.
     */
    public const MINUTOS_RESERVA_CARRITO = 15;

    /**
     * Crea una reserva de stock vinculada a un pedido.
     *
     * Para cada línea del pedido descuenta la cantidad de la existencia en la
     * sucursal de origen y la marca como "activa" en reservas_stock.
     * Si la línea tiene PiezasÚnicas disponibles, la primera se marca como
     * "reservada" individualmente.
     *
     * @throws RuntimeException Si no hay stock suficiente en alguna línea.
     */
    public function crearReserva(Pedido $pedido, int $minutosExpiracion = 30): ReservaStock
    {
        return DB::transaction(function () use ($pedido, $minutosExpiracion): ReservaStock {
            $detalles = $pedido->detalles()->with(['varianteSku.existencias'])->get();

            if ($detalles->isEmpty()) {
                throw new RuntimeException(
                    "El pedido {$pedido->folio} no tiene líneas de detalle para reservar."
                );
            }

            /** @var ReservaStock|null $ultimaReservaCreada */
            $ultimaReservaCreada = null;

            foreach ($detalles as $detalle) {
                /** @var Existencia $existencia */
                $existencia = Existencia::query()
                    ->where('variante_sku_id', $detalle->variante_sku_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($existencia->cantidad < $detalle->cantidad) {
                    throw new RuntimeException(
                        "Stock insuficiente para el SKU {$detalle->variante_sku_id} en el pedido {$pedido->folio}. "
                        ."Disponible: {$existencia->cantidad}, Solicitado: {$detalle->cantidad}."
                    );
                }

                // Descontar la existencia disponible.
                $existencia->cantidad -= $detalle->cantidad;
                $existencia->save();

                // Marcar la primera pieza única disponible como "reservada", si existe.
                $piezaUnica = PiezaUnica::query()
                    ->where('existencia_id', $existencia->id)
                    ->where('estado', 'disponible')
                    ->lockForUpdate()
                    ->first();

                if ($piezaUnica) {
                    $piezaUnica->update(['estado' => 'reservada']);
                }

                $ultimaReservaCreada = ReservaStock::create([
                    'existencia_id' => $existencia->id,
                    'pedido_id' => $pedido->id,
                    'carrito_id' => null,
                    'cantidad' => $detalle->cantidad,
                    'expira_en' => Carbon::now()->addMinutes($minutosExpiracion),
                    'estado' => 'activa',
                ]);
            }

            Log::info("[ReservaService] Reserva creada para el pedido {$pedido->folio}.", [
                'pedido_id' => $pedido->id,
                'minutos_expiracion' => $minutosExpiracion,
            ]);

            return $ultimaReservaCreada;
        });
    }

    /**
     * Libera todas las reservas de stock que han superado su fecha de expiración
     * y que aún se encuentran en estado "activa".
     *
     * Restaura la cantidad reservada a la existencia correspondiente, revierte
     * el estado de las PiezasÚnicas a "disponible" y marca la reserva como
     * "expirada" dentro de una transacción atómica por cada reserva.
     *
     * @return int Número de reservas efectivamente liberadas.
     */
    public function liberarReservasVencidas(): int
    {
        $liberadas = 0;

        /** @var Collection<int, ReservaStock> $reservasVencidas */
        $reservasVencidas = ReservaStock::query()
            ->where('estado', 'activa')
            ->where('expira_en', '<=', Carbon::now())
            ->lockForUpdate()
            ->get();

        foreach ($reservasVencidas as $reserva) {
            DB::transaction(function () use ($reserva): void {
                /** @var Existencia|null $existencia */
                $existencia = Existencia::query()
                    ->lockForUpdate()
                    ->find($reserva->existencia_id);

                if ($existencia) {
                    $existencia->cantidad += $reserva->cantidad;
                    $existencia->save();

                    // Revertir el estado de la pieza única asociada al pedido, si aplica.
                    if ($reserva->pedido_id) {
                        $piezaUnica = PiezaUnica::query()
                            ->where('existencia_id', $existencia->id)
                            ->where('estado', 'reservada')
                            ->lockForUpdate()
                            ->first();

                        if ($piezaUnica) {
                            $piezaUnica->update(['estado' => 'disponible']);
                        }
                    }
                }

                $reserva->update(['estado' => 'expirada']);
            });

            $liberadas++;
        }

        if ($liberadas > 0) {
            Log::info("[ReservaService] Se liberaron {$liberadas} reservas de stock vencidas.", [
                'timestamp' => Carbon::now()->toDateTimeString(),
            ]);
        }

        return $liberadas;
    }

    /**
     * Convierte una reserva confirmada en un movimiento de salida definitiva en
     * movimientos_inventario cuando el pedido es entregado.
     *
     * La existencia ya fue descontada al crear la reserva, por lo que este método
     * únicamente registra el movimiento contable y marca la reserva como "liberada"
     * (consumida). Las PiezasÚnicas pasan a estado "vendida".
     *
     * @throws RuntimeException Si el pedido no tiene reservas confirmadas.
     */
    public function convertirReservaEnSalidaDefinitiva(Pedido $pedido): void
    {
        DB::transaction(function () use ($pedido): void {
            $reservas = ReservaStock::query()
                ->where('pedido_id', $pedido->id)
                ->where('estado', 'confirmada')
                ->lockForUpdate()
                ->get();

            if ($reservas->isEmpty()) {
                throw new RuntimeException(
                    "No se encontraron reservas confirmadas para el pedido {$pedido->folio}."
                );
            }

            foreach ($reservas as $reserva) {
                // Registrar el movimiento de salida definitiva en el libro de inventario.
                MovimientoInventario::create([
                    'existencia_id' => $reserva->existencia_id,
                    'usuario_id' => auth()->id(),
                    'tipo' => 'salida',
                    'cantidad' => -abs($reserva->cantidad),
                    'motivo' => "Salida definitiva por entrega del pedido {$pedido->folio}.",
                    'referencia_type' => Pedido::class,
                    'referencia_id' => $pedido->id,
                    'created_at' => now(),
                ]);

                // Marcar la pieza única reservada (si existe) como vendida definitivamente.
                $piezaUnica = PiezaUnica::query()
                    ->where('existencia_id', $reserva->existencia_id)
                    ->where('estado', 'reservada')
                    ->lockForUpdate()
                    ->first();

                if ($piezaUnica) {
                    $piezaUnica->update(['estado' => 'vendida']);
                }

                // Cerrar la reserva como consumida.
                $reserva->update([
                    'estado' => 'liberada',
                    'expira_en' => null,
                ]);
            }

            Log::info("[ReservaService] Reservas convertidas en salida definitiva para {$pedido->folio}.", [
                'pedido_id' => $pedido->id,
                'total_reservas' => $reservas->count(),
            ]);
        });
    }

    // -------------------------------------------------------------------------
    // Métodos heredados del servicio original (compatibilidad con middleware)
    // -------------------------------------------------------------------------

    /**
     * Crea una reserva temporal de stock para un carrito de compras.
     *
     * @throws RuntimeException Si no hay stock suficiente para reservar.
     */
    public function reservarParaCarrito(
        int $existenciaId,
        int $carritoId,
        int $cantidad,
        ?int $minutosVigencia = null
    ): ReservaStock {
        $minutosVigencia ??= self::MINUTOS_RESERVA_CARRITO;

        return DB::transaction(function () use ($existenciaId, $carritoId, $cantidad, $minutosVigencia): ReservaStock {
            /** @var Existencia $existencia */
            $existencia = Existencia::query()->lockForUpdate()->findOrFail($existenciaId);

            if ($existencia->cantidad < $cantidad) {
                throw new RuntimeException(
                    "Stock insuficiente para reservar. Solicitado: {$cantidad}, Disponible: {$existencia->cantidad}."
                );
            }

            $existencia->cantidad -= $cantidad;
            $existencia->save();

            return ReservaStock::create([
                'existencia_id' => $existenciaId,
                'carrito_id' => $carritoId,
                'cantidad' => $cantidad,
                'expira_en' => Carbon::now()->addMinutes($minutosVigencia),
                'estado' => 'activa',
            ]);
        });
    }

    /**
     * Confirma una reserva existente vinculándola a un pedido (cambia estado a "confirmada").
     * La existencia ya fue descontada al reservar, por lo que no se modifica nuevamente.
     */
    public function confirmarReserva(ReservaStock $reserva, int $pedidoId): ReservaStock
    {
        $reserva->update([
            'pedido_id' => $pedidoId,
            'estado' => 'confirmada',
            'expira_en' => null,
        ]);

        return $reserva->fresh();
    }

    /**
     * Libera manualmente una reserva activa, devolviendo el stock a la existencia.
     */
    public function liberarReserva(ReservaStock $reserva): bool
    {
        if ($reserva->estado !== 'activa') {
            return false;
        }

        return DB::transaction(function () use ($reserva): bool {
            /** @var Existencia|null $existencia */
            $existencia = Existencia::query()->lockForUpdate()->find($reserva->existencia_id);

            if ($existencia) {
                $existencia->cantidad += $reserva->cantidad;
                $existencia->save();
            }

            $reserva->update(['estado' => 'liberada']);

            return true;
        });
    }
}
