<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega la columna `precio_unitario_congelado` a `carrito_items` para respetar
     * el precio vigente al momento de agregar un Producto Común durante sus 7 días de vigencia.
     * Las Piezas Únicas mantienen este campo en NULL para reflejar siempre su precio actual.
     */
    public function up(): void
    {
        if (Schema::hasTable('carrito_items') && ! Schema::hasColumn('carrito_items', 'precio_unitario_congelado')) {
            Schema::table('carrito_items', function (Blueprint $table): void {
                $table->decimal('precio_unitario_congelado', 12, 2)->nullable()->after('cantidad');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('carrito_items') && Schema::hasColumn('carrito_items', 'precio_unitario_congelado')) {
            Schema::table('carrito_items', function (Blueprint $table): void {
                $table->dropColumn('precio_unitario_congelado');
            });
        }
    }
};
