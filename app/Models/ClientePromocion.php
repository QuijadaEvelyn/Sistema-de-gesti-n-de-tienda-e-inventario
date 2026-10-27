<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ClientePromocion extends Pivot
{
    protected $table = 'cliente_promocion';

    public $incrementing = false;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'cliente_frecuente_id',
        'promocion_id',
        'recibida_en',
        'usada_en',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'recibida_en' => 'datetime',
            'usada_en' => 'datetime',
        ];
    }

    /**
     * Cliente frecuente asociado a la asignación de la promoción.
     */
    public function clienteFrecuente(): BelongsTo
    {
        return $this->belongsTo(ClienteFrecuente::class, 'cliente_frecuente_id', 'cliente_id');
    }

    /**
     * Promoción asignada.
     */
    public function promocion(): BelongsTo
    {
        return $this->belongsTo(Promocion::class, 'promocion_id', 'id');
    }
}
