<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

class Promocion extends Model
{
    use HasFactory;

    protected $table = 'promociones';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'producto_id',
        'nombre',
        'descripcion',
        'tipo_descuento',
        'valor',
        'es_preventa_exclusiva',
        'preventa_inicia_en',
        'apertura_publica_en',
        'badge_etiqueta',
        'inicia_en',
        'termina_en',
        'activa',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'es_preventa_exclusiva' => 'boolean',
            'preventa_inicia_en' => 'datetime',
            'apertura_publica_en' => 'datetime',
            'inicia_en' => 'date',
            'termina_en' => 'date',
            'activa' => 'boolean',
        ];
    }

    /**
     * Producto destacado vinculado a la promoción / preventa (opcional).
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    /**
     * Clientes frecuentes con acceso a esta promoción.
     */
    public function clientesFrecuentes(): BelongsToMany
    {
        return $this->belongsToMany(ClienteFrecuente::class, 'cliente_promocion', 'promocion_id', 'cliente_frecuente_id')
            ->using(ClientePromocion::class)
            ->withPivot(['recibida_en', 'usada_en']);
    }

    /**
     * Obtiene el instante en que inicia la Fase de Preventa Exclusiva para Clientes Frecuentes.
     */
    public function fechaInicioPreventa(): Carbon
    {
        if ($this->preventa_inicia_en instanceof Carbon) {
            return $this->preventa_inicia_en;
        }

        if ($this->inicia_en !== null) {
            return Carbon::parse($this->inicia_en)->startOfDay();
        }

        return now()->subDay();
    }

    /**
     * Obtiene el instante en que la promoción se desbloquea para el Público General.
     */
    public function fechaAperturaPublica(): Carbon
    {
        if ($this->apertura_publica_en instanceof Carbon) {
            return $this->apertura_publica_en;
        }

        if ($this->inicia_en !== null) {
            return Carbon::parse($this->inicia_en)->startOfDay();
        }

        return now()->subDay();
    }

    /**
     * Obtiene el instante en que expira la promoción.
     */
    public function fechaCierre(): Carbon
    {
        if ($this->termina_en !== null) {
            return Carbon::parse($this->termina_en)->endOfDay();
        }

        return now()->addDays(14)->endOfDay();
    }

    /**
     * Determina la fase operativa actual de la promoción:
     * - 'inactiva': desactivada manualmente
     * - 'expirada': ya superó la fecha de cierre
     * - 'programada': aún no inicia la preventa ni la apertura
     * - 'preventa_exclusiva': activa únicamente para Clientes Frecuentes (>= 5 compras)
     * - 'abierta_publico': desbloqueada para todo el público general
     */
    public function faseActual(): string
    {
        if (! $this->activa) {
            return 'inactiva';
        }

        $ahora = now();
        $cierre = $this->fechaCierre();

        if ($ahora->gt($cierre)) {
            return 'expirada';
        }

        $inicioPreventa = $this->fechaInicioPreventa();
        $aperturaPublica = $this->fechaAperturaPublica();

        if ($this->es_preventa_exclusiva) {
            if ($ahora->lt($inicioPreventa)) {
                return 'programada';
            }

            if ($ahora->lt($aperturaPublica)) {
                return 'preventa_exclusiva';
            }

            return 'abierta_publico';
        }

        if ($ahora->lt($aperturaPublica)) {
            return 'programada';
        }

        return 'abierta_publico';
    }

    /**
     * Indica si la promoción se encuentra actualmente en Fase de Preventa Exclusiva para Clientes Frecuentes.
     */
    public function estaEnPreventaExclusiva(): bool
    {
        return $this->faseActual() === 'preventa_exclusiva';
    }

    /**
     * Indica si la promoción ya se encuentra en Fase de Apertura para todo el Público General.
     */
    public function estaAbiertaAlPublico(): bool
    {
        return $this->faseActual() === 'abierta_publico';
    }

    /**
     * Determina si un usuario o cliente específico puede adquirir la pieza de esta promoción en el momento actual.
     */
    public function puedeAdquirir(?Usuario $usuario = null, ?Cliente $cliente = null): bool
    {
        $fase = $this->faseActual();

        if ($fase === 'abierta_publico') {
            return true;
        }

        if ($fase === 'preventa_exclusiva') {
            if ($usuario !== null && method_exists($usuario, 'esAdmin') && $usuario->esAdmin()) {
                return true;
            }

            $clienteResuelto = $cliente ?? ($usuario !== null ? Cliente::where('usuario_id', $usuario->id)->first() : null);

            return $clienteResuelto !== null && $clienteResuelto->esClienteFrecuente();
        }

        return false;
    }

    /**
     * Devuelve el timestamp en milisegundos hacia el cual debe contar el reloj regresivo (countdown) en tiempo real.
     */
    public function timestampObjetivoCountdownMs(): int
    {
        $fase = $this->faseActual();

        $objetivo = match ($fase) {
            'programada' => $this->fechaInicioPreventa(),
            'preventa_exclusiva' => $this->fechaAperturaPublica(),
            default => $this->fechaCierre(),
        };

        return $objetivo->getTimestamp() * 1000;
    }

    /**
     * Etiqueta descriptiva de la fase actual para mostrar en insignias y banners.
     */
    public function etiquetaFase(): string
    {
        return match ($this->faseActual()) {
            'preventa_exclusiva' => 'PREVENTA EXCLUSIVA VIP · CLIENTES FRECUENTES (5+ COMPRAS)',
            'abierta_publico' => 'APERTURA OFICIAL · PÚBLICO GENERAL',
            'programada' => 'LANZAMIENTO PROGRAMADO · PRÓXIMAMENTE',
            'expirada' => 'PROMOCIÓN CONCLUIDA',
            default => 'PROMOCIÓN INACTIVA',
        };
    }

    /**
     * Etiqueta explicativa del contador regresivo según la fase actual.
     */
    public function etiquetaCountdown(): string
    {
        return match ($this->faseActual()) {
            'preventa_exclusiva' => 'Apertura al Público General en:',
            'abierta_publico' => 'La Promoción Expira en:',
            'programada' => 'Inicio de Preventa Anticipada en:',
            default => 'Tiempo Concluido',
        };
    }

    /**
     * Calcula el precio promocional aplicando el descuento configurado sobre el precio base.
     */
    public function calcularPrecioPromocional(float $precioOriginal): float
    {
        if ($precioOriginal <= 0.0) {
            return 0.0;
        }

        $descuento = match ($this->tipo_descuento) {
            'porcentaje' => $precioOriginal * ((float) $this->valor / 100.0),
            'monto' => min((float) $this->valor, $precioOriginal),
            default => 0.0,
        };

        return round(max(0.0, $precioOriginal - $descuento), 2);
    }
}
