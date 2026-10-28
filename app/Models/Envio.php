<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Envio extends Model
{
    use HasFactory;

    protected $table = 'envios';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'pedido_id',
        'sucursal_id',
        'paqueteria',
        'numero_guia',
        'estado_actual',
        'fecha_estimada_entrega',
        'estatus',
        'enviado_en',
        'entregado_en',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_estimada_entrega' => 'datetime',
            'enviado_en' => 'datetime',
            'entregado_en' => 'datetime',
        ];
    }

    /**
     * Pedido al que corresponde el paquete despachado.
     */
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    /**
     * Sucursal física o almacén de origen desde donde se emitió el paquete.
     */
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    /**
     * Artículos incluidos en este paquete o guía de envío.
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleEnvio::class, 'envio_id');
    }

    /**
     * Historial de eventos y actualizaciones de rastreo tipo Amazon del envío.
     */
    public function historial(): HasMany
    {
        return $this->hasMany(HistorialEnvio::class, 'envio_id');
    }
}
