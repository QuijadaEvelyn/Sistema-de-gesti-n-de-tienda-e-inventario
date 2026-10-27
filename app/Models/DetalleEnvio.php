<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleEnvio extends Model
{
    use HasFactory;

    protected $table = 'detalle_envios';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'envio_id',
        'detalle_pedido_id',
        'cantidad',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cantidad' => 'integer',
        ];
    }

    /**
     * Envió de paquetería al que está vinculado este bulto o contenido.
     */
    public function envio(): BelongsTo
    {
        return $this->belongsTo(Envio::class, 'envio_id');
    }

    /**
     * Línea de detalle del pedido que fue empacada en el envío.
     */
    public function detallePedido(): BelongsTo
    {
        return $this->belongsTo(DetallePedido::class, 'detalle_pedido_id');
    }
}
