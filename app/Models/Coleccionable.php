<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Coleccionable extends Model
{
    use HasFactory;

    protected $table = 'coleccionables';

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
        'marca',
        'serie',
        'material',
        'edicion_limitada',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'edicion_limitada' => 'boolean',
        ];
    }

    /**
     * Producto base al que extiende la información de memorabilia/coleccionable.
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id');
    }
}
