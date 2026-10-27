<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MovimientoInventario extends Model
{
    use HasFactory;

    protected $table = 'movimientos_inventario';

    /**
     * Esta tabla únicamente registra fecha de creación, sin fecha de actualización.
     */
    public const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'existencia_id',
        'usuario_id',
        'tipo',
        'cantidad',
        'motivo',
        'referencia_type',
        'referencia_id',
        'created_at',
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
            'created_at' => 'datetime',
        ];
    }

    /**
     * Registro de existencia afectado por el movimiento.
     */
    public function existencia(): BelongsTo
    {
        return $this->belongsTo(Existencia::class, 'existencia_id');
    }

    /**
     * Usuario que registró el movimiento (si aplica).
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Modelo polimórfico de referencia (ej. Pedido, Devolucion, etc.).
     */
    public function referencia(): MorphTo
    {
        return $this->morphTo();
    }
}
