<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetallePago extends Model
{
    use HasFactory;

    protected $table = 'detalle_pagos';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'pago_id',
        'referencia_externa',
        'ultimos_digitos',
        'autorizacion',
        'respuesta_pasarela',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'respuesta_pasarela' => 'array',
        ];
    }

    /**
     * Pago principal al que pertenece el detalle de pasarela.
     */
    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class, 'pago_id');
    }
}
