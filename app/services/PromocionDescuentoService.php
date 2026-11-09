<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Cliente;
use App\Models\ClienteFrecuente;
use App\Models\ClientePromocion;
use App\Models\Promocion;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class PromocionDescuentoService
{
    /**
     * Valida que una promocion exista, este activa y dentro de su periodo de vigencia.
     * Si se proporciona un cliente frecuente, valida ademas que tenga la promocion asignada
     * y que no haya sido utilizada previamente.
     *
     *
     * @throws RuntimeException
     */
    public function validarPromocion(int $promocionId, ?int $clienteFrecuenteId = null): Promocion
    {
        /** @var Promocion|null $promocion */
        $promocion = Promocion::find($promocionId);

        if ($promocion === null) {
            throw new RuntimeException("La promocion con ID {$promocionId} no existe.");
        }

        if (! $promocion->activa) {
            throw new RuntimeException("La promocion '{$promocion->nombre}' se encuentra inactiva.");
        }

        $hoy = Carbon::today();

        if ($promocion->inicia_en && $hoy->lt($promocion->inicia_en)) {
            throw new RuntimeException("La promocion '{$promocion->nombre}' aun no inicia su vigencia.");
        }

        if ($promocion->termina_en && $hoy->gt($promocion->termina_en)) {
            throw new RuntimeException("La promocion '{$promocion->nombre}' ha expirado.");
        }

        if ($clienteFrecuenteId !== null) {
            $asignacion = ClientePromocion::where('cliente_frecuente_id', $clienteFrecuenteId)
                ->where('promocion_id', $promocionId)
                ->first();

            if ($asignacion === null) {
                throw new RuntimeException("El cliente no tiene asignada la promocion '{$promocion->nombre}'.");
            }

            if ($asignacion->usada_en !== null) {
                throw new RuntimeException("La promocion '{$promocion->nombre}' ya fue utilizada por este cliente.");
            }
        }

        return $promocion;
    }

    /**
     * Calcula el monto de descuento efectivo para un subtotal dado segun las reglas de la promocion.
     */
    public function calcularMontoDescuento(Promocion $promocion, float $subtotal): float
    {
        if ($subtotal <= 0.0) {
            return 0.0;
        }

        $descuento = match ($promocion->tipo_descuento) {
            'porcentaje' => $subtotal * ((float) $promocion->valor / 100.0),
            'monto' => min((float) $promocion->valor, $subtotal),
            default => 0.0,
        };

        return round(min($descuento, $subtotal), 2);
    }

    /**
     * Marca una promocion asignada a un cliente frecuente como utilizada en la fecha actual.
     *
     *
     * @throws RuntimeException
     */
    public function marcarComoUsada(int $promocionId, int $clienteFrecuenteId): void
    {
        DB::transaction(function () use ($promocionId, $clienteFrecuenteId): void {
            $affected = ClientePromocion::where('cliente_frecuente_id', $clienteFrecuenteId)
                ->where('promocion_id', $promocionId)
                ->whereNull('usada_en')
                ->update(['usada_en' => Carbon::now()]);

            if ($affected === 0) {
                throw new RuntimeException('No se encontro una asignacion activa y pendiente de uso para esta promocion.');
            }
        });
    }

    /**
     * Obtiene el listado de promociones activas y no utilizadas asignadas a un cliente frecuente.
     *
     * @return Collection<int, Promocion>
     */
    public function promocionesActivasParaCliente(int $clienteFrecuenteId): Collection
    {
        $hoy = Carbon::today()->toDateString();

        /** @var ClienteFrecuente|null $clienteFrecuente */
        $clienteFrecuente = ClienteFrecuente::find($clienteFrecuenteId);

        if ($clienteFrecuente === null) {
            return collect();
        }

        return $clienteFrecuente->promociones()
            ->where('activa', true)
            ->whereDate('inicia_en', '<=', $hoy)
            ->where(function ($query) use ($hoy): void {
                $query->whereNull('termina_en')
                    ->orWhereDate('termina_en', '>=', $hoy);
            })
            ->wherePivotNull('usada_en')
            ->get();
    }

    /**
     * Evalúa si un cliente ha alcanzado las 5 compras mínimas y, en caso afirmativo,
     * activa su membresía de Cliente Frecuente y le asigna notificación/acceso anticipado
     * a todas las promociones y preventas vigentes.
     */
    public function sincronizarMembresiaPorCompras(Cliente $cliente): ?ClienteFrecuente
    {
        if (! Schema::hasTable('clientes_frecuentes')) {
            return null;
        }

        $totalCompras = $cliente->totalComprasRealizadas();

        if ($totalCompras < Cliente::COMPRAS_MINIMAS_FRECUENTE && ! $cliente->clienteFrecuente()->exists()) {
            return null;
        }

        $clienteFrecuente = ClienteFrecuente::updateOrCreate(
            ['cliente_id' => $cliente->id],
            [
                'desde' => $cliente->clienteFrecuente?->desde ?? Carbon::today(),
                'puntos' => max((int) ($cliente->clienteFrecuente?->puntos ?? 0), $totalCompras * 100),
            ]
        );

        if (Schema::hasTable('promociones') && Schema::hasTable('cliente_promocion')) {
            $promocionesVigentes = Promocion::where('activa', true)->get();
            foreach ($promocionesVigentes as $promo) {
                if (in_array($promo->faseActual(), ['preventa_exclusiva', 'abierta_publico', 'programada'], true)) {
                    ClientePromocion::firstOrCreate(
                        [
                            'cliente_frecuente_id' => (int) $clienteFrecuente->cliente_id,
                            'promocion_id' => $promo->id,
                        ],
                        [
                            'recibida_en' => Carbon::now(),
                        ]
                    );
                }
            }
        }

        return $clienteFrecuente;
    }

    /**
     * Asigna y notifica una promoción o preventa exclusiva a todos los clientes frecuentes
     * que cuenten con 5 o más compras completadas.
     */
    public function notificarPromocionAClientesFrecuentes(Promocion $promocion): int
    {
        if (! Schema::hasTable('clientes') || ! Schema::hasTable('clientes_frecuentes') || ! Schema::hasTable('cliente_promocion')) {
            return 0;
        }

        $notificados = 0;
        $clientes = Cliente::with(['pedidos', 'clienteFrecuente'])->get();

        foreach ($clientes as $cliente) {
            if ($cliente->esClienteFrecuente()) {
                $frecuente = $this->sincronizarMembresiaPorCompras($cliente);
                if ($frecuente !== null) {
                    ClientePromocion::firstOrCreate(
                        [
                            'cliente_frecuente_id' => (int) $frecuente->cliente_id,
                            'promocion_id' => $promocion->id,
                        ],
                        [
                            'recibida_en' => Carbon::now(),
                        ]
                    );
                    $notificados++;
                }
            }
        }

        return $notificados;
    }
}
