<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Promocion extends Model
{
    use HasFactory;

    protected $table = 'promociones';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo_descuento',
        'valor',
        'inicia_en',
        'termina_en',
        'activa',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'inicia_en' => 'date',
            'termina_en' => 'date',
            'activa' => 'boolean',
        ];
    }

    /**
     * Clientes frecuentes con acceso a esta promoción.
     */
    public function clientesFrecuentes(): BelongsToMany
    {
        return $this->belongsToMany(ClienteFrecuente::class, 'cliente_promocion', 'promocion_id', 'cliente_frecuente_id')
            ->using(ClientePromocion::class)
            ->withPivot(['recibida_en', 'usada_en']);
    }
}
