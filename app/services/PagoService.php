<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Bitacora;
use App\Models\DetallePago;
use App\Models\Pago;
use App\Models\Pedido;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Servicio que gestiona los métodos de pago alternativos al flujo estándar:
 * ficha de pago OXXO y depósito/transferencia bancaria.
 *
 * Ambos métodos crean un registro en `pagos` con estatus "pendiente" y
 * dejan el detalle técnico en `detalle_pagos`. La validación final
 * (cambiar estatus a "aprobado") queda en manos del equipo de Ventas o
 * de un webhook automático de la pasarela.
 */
class PagoService
{
    /**
     * Longitud del código numérico de referencia OXXO.
     */
    private const LONGITUD_CODIGO_OXXO = 18;

    /**
     * Días de vigencia que tendrá la ficha de pago OXXO desde su generación.
     */
    private const DIAS_VIGENCIA_OXXO = 3;

    /**
     * Genera una ficha de pago OXXO para el pedido indicado.
     *
     * Crea un registro en `pagos` con método "efectivo" y estatus "pendiente",
     * y registra en `detalle_pagos` el código numérico generado junto con su
     * fecha de expiración para que sea mostrado al cliente en pantalla/correo.
     *
     * Nota: En producción, el código y la referencia deberían generarse a través
     * de la API de una pasarela como Conekta/OpenPay. Aquí se implementa la lógica
     * de negocio sin depender de ningún SDK externo.
     *
     * @param  Pedido  $pedido  Pedido para el que se genera la ficha de pago.
     * @return array{pago: Pago, detalle: DetallePago, codigo_oxxo: string, expira_en: Carbon}
     *                                                                                         Retorna el objeto Pago, el DetallePago y los datos de la ficha.
     *
     * @throws RuntimeException Si el pedido ya tiene un pago pendiente de tipo efectivo.
     */
    public function generarFichaOxxo(Pedido $pedido): array
    {
        return DB::transaction(function () use ($pedido): array {
            // Verificar que no exista ya una ficha OXXO pendiente para este pedido.
            $pagoExistente = $pedido->pagos()
                ->where('metodo', 'efectivo')
                ->where('estatus', 'pendiente')
                ->exists();

            if ($pagoExistente) {
                throw new RuntimeException(
                    "El pedido {$pedido->folio} ya tiene una ficha de pago OXXO pendiente activa."
                );
            }

            $codigoOxxo = $this->generarCodigoOxxo();
            $expiraEn = Carbon::now()->addDays(self::DIAS_VIGENCIA_OXXO);

            /** @var Pago $pago */
            $pago = Pago::create([
                'pedido_id' => $pedido->id,
                'metodo' => 'efectivo',
                'monto' => $pedido->total,
                'estatus' => 'pendiente',
                'pagado_en' => null,
            ]);

            /** @var DetallePago $detalle */
            $detalle = DetallePago::create([
                'pago_id' => $pago->id,
                'referencia_externa' => $codigoOxxo,
                'ultimos_digitos' => null,
                'autorizacion' => null,
                'respuesta_pasarela' => [
                    'tipo' => 'oxxo_cash',
                    'codigo_oxxo' => $codigoOxxo,
                    'expira_en' => $expiraEn->toIso8601String(),
                    'monto' => (float) $pedido->total,
                    'moneda' => 'MXN',
                    'generado_en' => now()->toIso8601String(),
                ],
            ]);

            Bitacora::create([
                'usuario_id' => auth()->id(),
                'accion' => 'FICHA_OXXO_GENERADA',
                'tabla_afectada' => 'pagos',
                'registro_id' => $pago->id,
                'detalle' => [
                    'pedido_folio' => $pedido->folio,
                    'codigo_oxxo' => $codigoOxxo,
                    'expira_en' => $expiraEn->toDateTimeString(),
                    'monto' => (float) $pedido->total,
                ],
                'ip' => request()->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);

            Log::info("[PagoService] Ficha OXXO generada para el pedido {$pedido->folio}.", [
                'pago_id' => $pago->id,
                'codigo_oxxo' => $codigoOxxo,
                'expira_en' => $expiraEn->toDateTimeString(),
            ]);

            return [
                'pago' => $pago,
                'detalle' => $detalle,
                'codigo_oxxo' => $codigoOxxo,
                'expira_en' => $expiraEn,
            ];
        });
    }

    /**
     * Registra el pago por depósito o transferencia bancaria de un pedido.
     *
     * Crea un registro en `pagos` con método "transferencia" y estatus "pendiente"
     * (requiere validación manual por parte de Ventas), y guarda en `detalle_pagos`
     * la referencia bancaria y la URL del comprobante adjunto.
     *
     * @param  Pedido  $pedido  Pedido que se desea liquidar.
     * @param  string  $referencia  Folio o clave de rastreo del depósito bancario.
     * @param  string  $comprobanteUrl  URL donde se aloja el comprobante (imagen/PDF subido).
     * @return array{pago: Pago, detalle: DetallePago}
     *
     * @throws RuntimeException Si el pedido ya tiene una transferencia pendiente de validación.
     */
    public function registrarPagoTransferencia(
        Pedido $pedido,
        string $referencia,
        string $comprobanteUrl
    ): array {
        if (trim($referencia) === '') {
            throw new RuntimeException('La referencia bancaria no puede estar vacía.');
        }

        if (trim($comprobanteUrl) === '') {
            throw new RuntimeException('La URL del comprobante no puede estar vacía.');
        }

        return DB::transaction(function () use ($pedido, $referencia, $comprobanteUrl): array {
            // Evitar duplicar una solicitud de transferencia ya en revisión.
            $pagoExistente = $pedido->pagos()
                ->where('metodo', 'transferencia')
                ->where('estatus', 'pendiente')
                ->exists();

            if ($pagoExistente) {
                throw new RuntimeException(
                    "El pedido {$pedido->folio} ya tiene un pago por transferencia pendiente de validación."
                );
            }

            /** @var Pago $pago */
            $pago = Pago::create([
                'pedido_id' => $pedido->id,
                'metodo' => 'transferencia',
                'monto' => $pedido->total,
                'estatus' => 'pendiente',
                'pagado_en' => null,
            ]);

            /** @var DetallePago $detalle */
            $detalle = DetallePago::create([
                'pago_id' => $pago->id,
                'referencia_externa' => $referencia,
                'ultimos_digitos' => null,
                'autorizacion' => null,
                'respuesta_pasarela' => [
                    'tipo' => 'transferencia_bancaria',
                    'referencia' => $referencia,
                    'comprobante_url' => $comprobanteUrl,
                    'monto' => (float) $pedido->total,
                    'moneda' => 'MXN',
                    'registrado_en' => now()->toIso8601String(),
                    'validado' => false,
                ],
            ]);

            Bitacora::create([
                'usuario_id' => auth()->id(),
                'accion' => 'PAGO_TRANSFERENCIA_REGISTRADO',
                'tabla_afectada' => 'pagos',
                'registro_id' => $pago->id,
                'detalle' => [
                    'pedido_folio' => $pedido->folio,
                    'referencia' => $referencia,
                    'comprobante_url' => $comprobanteUrl,
                    'monto' => (float) $pedido->total,
                ],
                'ip' => request()->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);

            Log::info("[PagoService] Pago por transferencia registrado para el pedido {$pedido->folio}.", [
                'pago_id' => $pago->id,
                'referencia' => $referencia,
            ]);

            return [
                'pago' => $pago,
                'detalle' => $detalle,
            ];
        });
    }

    // -------------------------------------------------------------------------
    // Helpers privados
    // -------------------------------------------------------------------------

    /**
     * Genera un código numérico único de 18 dígitos para la ficha OXXO.
     *
     * En producción este número es emitido por la pasarela (Conekta, OpenPay, etc.),
     * pero para entornos sin integración se genera localmente con verificación de unicidad.
     */
    private function generarCodigoOxxo(): string
    {
        do {
            // Prefijo de identificación de comercio (2 dígitos) + timestamp (10) + aleatorio (6).
            $codigo = '14'.date('mdHis').str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (
            DetallePago::query()
                ->where('referencia_externa', $codigo)
                ->exists()
        );

        return $codigo;
    }
}
