<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Devolucion extends Model
{
    use HasFactory;

    protected $table = 'devoluciones';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'pedido_id',
        'cliente_id',
        'detalle_pedido_id',
        'cantidad',
        'motivo',
        'estatus',
        'monto_reembolso',
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
            'monto_reembolso' => 'decimal:2',
        ];
    }

    /**
     * Pedido sobre el cual se originó la devolución.
     */
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    /**
     * Cliente que solicita la devolución.
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    /**
     * Línea de detalle del pedido que es objeto de devolución.
     */
    public function detallePedido(): BelongsTo
    {
        return $this->belongsTo(DetallePedido::class, 'detalle_pedido_id');
    }
}
