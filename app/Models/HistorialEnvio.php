<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialEnvio extends Model
{
    use HasFactory;

    protected $table = 'historial_envios';

    /**
     * Esta tabla solo registra fecha de creacion para eventos cronologicos.
     */
    public const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'envio_id',
        'estado',
        'ubicacion',
        'descripcion',
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
            'created_at' => 'datetime',
        ];
    }

    /**
     * Envio de paqueteria al que pertenece este hito del historial.
     */
    public function envio(): BelongsTo
    {
        return $this->belongsTo(Envio::class, 'envio_id');
    }
}
