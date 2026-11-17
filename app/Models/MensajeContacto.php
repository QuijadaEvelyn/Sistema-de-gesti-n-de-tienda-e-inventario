<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MensajeContacto extends Model
{
    use HasFactory;

    protected $table = 'mensajes_contacto';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'sucursal_id',
        'asunto',
        'mensaje',
        'estatus',
        'ip',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sucursal_id' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Sede o sucursal de preferencia indicada por el usuario (si aplica).
     *
     * @return BelongsTo<Sucursal, $this>
     */
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    /**
     * Scope para filtrar por estatus.
     *
     * @param  Builder<MensajeContacto>  $query
     * @param  string|null  $estatus
     * @return Builder<MensajeContacto>
     */
    public function scopeEstatus(Builder $query, ?string $estatus): Builder
    {
        if (! empty($estatus)) {
            return $query->where('estatus', $estatus);
        }

        return $query;
    }
}
