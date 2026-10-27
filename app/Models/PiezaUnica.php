<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PiezaUnica extends Model
{
    use HasFactory;

    protected $table = 'piezas_unicas';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'existencia_id',
        'numero_serie',
        'certificado',
        'notas',
        'estado',
    ];

    /**
     * Existencia de inventario a la que corresponde este ejemplar individual.
     */
    public function existencia(): BelongsTo
    {
        return $this->belongsTo(Existencia::class, 'existencia_id');
    }
}
