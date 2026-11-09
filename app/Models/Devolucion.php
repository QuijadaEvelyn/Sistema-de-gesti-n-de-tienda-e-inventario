<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Devolucion extends Model
{
    use HasFactory;

    protected $table = 'devoluciones';

    /**
     * Catálogo oficial de motivos de devolución y garantía curatorial.
     *
     * @var array<string, string>
     */
    public const MOTIVOS_CATALOGO = [
        'producto_defectuoso' => 'Producto defectuoso o dañado en traslado',
        'pieza_incorrecta' => 'Pieza o edición incorrecta',
        'condicion_no_coincide' => 'Condición no coincide con la descripción curatorial',
        'sello_alterado' => 'Sellos de autenticidad o encapsulado comprometidos',
        'otro_defecto' => 'Otro defecto físico de conservación',
    ];

    /**
     * Etapas del flujo de gestión de devoluciones y garantías.
     *
     * @var array<string, string>
     */
    public const ETAPAS = [
        'solicitada' => 'Pendiente de revisión',
        'aprobada' => 'Aprobada · Guía emitida',
        'rechazada' => 'Rechazada',
        'producto_retornado' => 'Producto retornado',
        'reembolso_procesado' => 'Reembolso procesado',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'folio_devolucion',
        'pedido_id',
        'cliente_id',
        'detalle_pedido_id',
        'cantidad',
        'motivo',
        'comentarios_cliente',
        'evidencias',
        'estatus',
        'etapa_devolucion',
        'monto_reembolso',
        'dictamen_admin',
        'guia_retorno',
        'instrucciones_retorno',
        'destino_inventario',
        'sucursal_destino_id',
        'nueva_graduacion',
        'stock_procesado',
        'revisado_por',
        'aprobada_en',
        'recibida_en',
        'reembolsada_en',
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
            'monto_reembolso' => 'decimal:2',
            'evidencias' => 'array',
            'stock_procesado' => 'boolean',
            'aprobada_en' => 'datetime',
            'recibida_en' => 'datetime',
            'reembolsada_en' => 'datetime',
        ];
    }

    /**
     * Pedido sobre el cual se originó la devolución.
     */
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    /**
     * Cliente que solicita la devolución.
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    /**
     * Línea de detalle del pedido que es objeto de devolución.
     */
    public function detallePedido(): BelongsTo
    {
        return $this->belongsTo(DetallePedido::class, 'detalle_pedido_id');
    }

    /**
     * Sucursal en la que se reintegró el stock o se registró la merma.
     */
    public function sucursalDestino(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_destino_id');
    }

    /**
     * Usuario administrativo o de ventas que revisó la solicitud.
     */
    public function revisor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'revisado_por');
    }

    /**
     * Devuelve el código de etapa operativa actual de la devolución.
     */
    public function estadoOperativo(): string
    {
        if (! empty($this->etapa_devolucion)) {
            return (string) $this->etapa_devolucion;
        }

        return match ((string) $this->estatus) {
            'completada' => 'reembolso_procesado',
            default => (string) ($this->estatus ?: 'solicitada'),
        };
    }

    /**
     * Devuelve la etiqueta legible del estado actual de la devolución.
     */
    public function etiquetaEstado(): string
    {
        $etapa = $this->estadoOperativo();

        return self::ETAPAS[$etapa] ?? ucfirst(str_replace('_', ' ', $etapa));
    }

    /**
     * Devuelve la etiqueta legible del motivo de devolución.
     */
    public function etiquetaMotivo(): string
    {
        $motivo = (string) $this->motivo;

        return self::MOTIVOS_CATALOGO[$motivo] ?? $motivo;
    }
}
