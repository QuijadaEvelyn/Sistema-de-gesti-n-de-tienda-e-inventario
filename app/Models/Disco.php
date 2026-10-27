<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Disco extends Model
{
    use HasFactory;

    protected $table = 'discos';

    /**
     * La clave primaria es producto_id.
     */
    protected $primaryKey = 'producto_id';

    public $incrementing = false;

    /**
     * Esta tabla no utiliza marcas de tiempo (timestamps).
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'producto_id',
        'artista',
        'sello',
        'genero',
        'formato',
        'anio_lanzamiento',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'anio_lanzamiento' => 'integer',
        ];
    }

    /**
     * Producto base al que extiende la información discográfica.
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id');
    }
}
