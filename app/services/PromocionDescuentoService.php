<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ClienteFrecuente;
use App\Models\ClientePromocion;
use App\Models\Promocion;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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
}
