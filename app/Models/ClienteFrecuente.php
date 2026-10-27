<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ClienteFrecuente extends Model
{
    use HasFactory;

    protected $table = 'clientes_frecuentes';

    /**
     * La clave primaria no es autoincremental, corresponde a cliente_id.
     */
    protected $primaryKey = 'cliente_id';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'cliente_id',
        'desde',
        'puntos',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'desde' => 'date',
            'puntos' => 'integer',
        ];
    }

    /**
     * Cliente base al que especializa este registro de fidelización.
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id');
    }

    /**
     * Promociones asignadas al cliente frecuente.
     */
    public function promociones(): BelongsToMany
    {
        return $this->belongsToMany(Promocion::class, 'cliente_promocion', 'cliente_frecuente_id', 'promocion_id')
            ->using(ClientePromocion::class)
            ->withPivot(['recibida_en', 'usada_en']);
    }
}
