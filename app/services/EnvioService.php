<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Bitacora;
use App\Models\DetalleEnvio;
use App\Models\Envio;
use App\Models\HistorialEnvio;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Servicio que gestiona el ciclo de vida de los envios de paqueteria, desde
 * la creacion de la guia hasta el registro de hitos de rastreo tipo Amazon.
 *
 * La tabla `envios` almacena el encabezado del paquete y `detalle_envios`
 * contiene los articulos incluidos en cada guia. El historial de rastreo
 * se persiste en la bitacora del sistema con todos los hitos de seguimiento.
 */
class EnvioService
{
    /**
     * Prefijo utilizado para generar el numero de guia interno.
     */
    private const PREFIJO_GUIA = 'GUA';

    /**
     * Crea el registro de envio para un pedido, asignando un numero de guia
     * unico y creando las lineas de `detalle_envios` por cada item del pedido.
     *
     * @param  Pedido  $pedido  Pedido que se va a despachar.
     * @param  string  $direccionEntrega  Direccion fisica de entrega al destinatario.
     * @param  string  $paqueteria  Nombre de la paqueteria transportista.
     * @param  int|null  $sucursalId  Sucursal de origen del envio.
     * @return Envio Registro de envio recien creado.
     *
     * @throws RuntimeException Si el pedido no tiene articulos o ya cuenta con un envio activo.
     */
    public function crearEnvio(
        Pedido $pedido,
        string $direccionEntrega,
        string $paqueteria = 'Por definir',
        ?int $sucursalId = null
    ): Envio {
        return DB::transaction(function () use ($pedido, $direccionEntrega, $paqueteria, $sucursalId): Envio {
            $detalles = $pedido->detalles()->get();

            if ($detalles->isEmpty()) {
                throw new RuntimeException(
                    "No se puede crear un envio para el pedido {$pedido->folio} porque no tiene articulos."
                );
            }

            // Verificar que no exista ya un envio en preparacion o transito para este pedido.
            $envioExistente = $pedido->envios()
                ->whereIn('estatus', ['preparando', 'en_transito'])
                ->exists();

            if ($envioExistente) {
                throw new RuntimeException(
                    "El pedido {$pedido->folio} ya cuenta con un envio activo en preparacion o transito."
                );
            }

            // Generar numero de guia unico garantizado.
            $numeroGuia = $this->generarNumeroGuia();

            /** @var Envio $envio */
            $envio = Envio::create([
                'pedido_id' => $pedido->id,
                'sucursal_id' => $sucursalId,
                'paqueteria' => $paqueteria,
                'numero_guia' => $numeroGuia,
                'estado_actual' => 'en_preparacion',
                'fecha_estimada_entrega' => now()->addDays(3),
                'estatus' => 'preparando',
                'enviado_en' => null,
                'entregado_en' => null,
            ]);

            // Registrar primer hito en el historial de rastreo
            HistorialEnvio::create([
                'envio_id' => $envio->id,
                'estado' => 'en_preparacion',
                'ubicacion' => 'Almacén de origen',
                'descripcion' => 'Guía de envío generada y paquete en preparación.',
                'created_at' => now(),
            ]);

            // Crear el detalle de envio por cada linea del pedido.
            foreach ($detalles as $detalle) {
                DetalleEnvio::create([
                    'envio_id' => $envio->id,
                    'detalle_pedido_id' => $detalle->id,
                    'cantidad' => $detalle->cantidad,
                ]);
            }

            // El estatus del pedido permanece "pagado" mientras el envio esta en preparacion.
            // Se actualizara a "enviado" cuando el paquete pase a "en_transito".

            Bitacora::create([
                'usuario_id' => auth()->id(),
                'accion' => 'ENVIO_CREADO',
                'tabla_afectada' => 'envios',
                'registro_id' => $envio->id,
                'detalle' => [
                    'pedido_folio' => $pedido->folio,
                    'numero_guia' => $numeroGuia,
                    'paqueteria' => $paqueteria,
                    'direccion_entrega' => $direccionEntrega,
                    'total_articulos' => $detalles->count(),
                ],
                'ip' => request()->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);

            Log::info("[EnvioService] Envio creado para el pedido {$pedido->folio}.", [
                'envio_id' => $envio->id,
                'numero_guia' => $numeroGuia,
            ]);

            return $envio;
        });
    }

    /**
     * Agrega un hito al historial de rastreo del envio, actualizando el estatus
     * del paquete junto con la ubicacion actual y un comentario descriptivo.
     *
     * Cada llamada actualiza el estatus en la tabla `envios` y persiste el hito
     * en la bitacora de auditoria, generando un historial completo tipo Amazon.
     *
     * @param  int  $envioId  ID del envio a actualizar.
     * @param  string  $estado  Nuevo estatus del paquete:
     *                          'preparando' | 'en_transito' | 'entregado' | 'devuelto'.
     * @param  string  $ubicacion  Ciudad, almacen o punto de distribucion actual.
     * @param  string  $comentario  Descripcion del evento (ej. "En camino al domicilio").
     * @return Envio Envio actualizado con el nuevo estatus.
     *
     * @throws RuntimeException Si el ID del envio no existe o el estatus es invalido.
     */
    public function actualizarUbicacionRastreo(
        int $envioId,
        string $estado,
        string $ubicacion,
        string $comentario
    ): Envio {
        $estatusValidos = [
            'preparando',
            'en_transito',
            'entregado',
            'devuelto',
            'pendiente',
            'en_preparacion',
            'en_camino',
            'incidencia',
        ];

        if (! in_array($estado, $estatusValidos, true)) {
            throw new RuntimeException(
                "Estatus de rastreo invalido: '{$estado}'. Valores permitidos: "
                .implode(', ', $estatusValidos).'.'
            );
        }

        $mapaEstadoActual = [
            'preparando' => 'en_preparacion',
            'en_preparacion' => 'en_preparacion',
            'en_transito' => 'en_transito',
            'en_camino' => 'en_camino',
            'entregado' => 'entregado',
            'devuelto' => 'incidencia',
            'incidencia' => 'incidencia',
            'pendiente' => 'pendiente',
        ];

        return DB::transaction(function () use ($envioId, $estado, $ubicacion, $comentario, $mapaEstadoActual): Envio {
            /** @var Envio $envio */
            $envio = Envio::query()->lockForUpdate()->findOrFail($envioId);

            $hitoRastreo = [
                'timestamp' => now()->toIso8601String(),
                'estado' => $estado,
                'ubicacion' => $ubicacion,
                'comentario' => $comentario,
            ];

            $cambios = [
                'estatus' => in_array($estado, ['preparando', 'en_transito', 'entregado', 'devuelto'], true)
                    ? $estado
                    : ($estado === 'en_camino' ? 'en_transito' : 'devuelto'),
                'estado_actual' => $mapaEstadoActual[$estado] ?? 'en_transito',
            ];

            // Registrar la marca de tiempo cuando el paquete sale por primera vez.
            if (in_array($estado, ['en_transito', 'en_camino'], true)) {
                if (! $envio->enviado_en) {
                    $cambios['enviado_en'] = now();
                }
                $envio->pedido?->update(['estatus' => 'enviado']);
            }

            // Registrar la marca de tiempo de entrega definitiva.
            if ($estado === 'entregado') {
                $cambios['entregado_en'] = now();
                $envio->pedido?->update(['estatus' => 'entregado']);
            }

            $envio->update($cambios);

            // Registrar hito cronológico en historial_envios
            HistorialEnvio::create([
                'envio_id' => $envio->id,
                'estado' => $estado,
                'ubicacion' => $ubicacion,
                'descripcion' => $comentario,
                'created_at' => now(),
            ]);

            // Persistir el hito en la bitacora de auditoria como historial de rastreo.
            Bitacora::create([
                'usuario_id' => auth()->id(),
                'accion' => 'RASTREO_ACTUALIZADO',
                'tabla_afectada' => 'envios',
                'registro_id' => $envio->id,
                'detalle' => [
                    'numero_guia' => $envio->numero_guia,
                    'hito' => $hitoRastreo,
                ],
                'ip' => request()->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);

            Log::info("[EnvioService] Hito de rastreo agregado al envio #{$envioId}.", [
                'estado' => $estado,
                'ubicacion' => $ubicacion,
            ]);

            return $envio->fresh();
        });
    }

    // -------------------------------------------------------------------------
    // Helpers privados
    // -------------------------------------------------------------------------

    /**
     * Genera un numero de guia unico con formato `GUA-YYYYMMDD-XXXXXXXX`.
     *
     * Reintenta hasta encontrar uno no existente en la base de datos.
     */
    private function generarNumeroGuia(): string
    {
        do {
            $guia = self::PREFIJO_GUIA.'-'.date('Ymd').'-'.strtoupper(Str::random(8));
        } while (Envio::query()->where('numero_guia', $guia)->exists());

        return $guia;
    }
}
