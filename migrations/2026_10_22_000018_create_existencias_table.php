<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('existencias', function (Blueprint $t) {
            // Inventario: cantidad de cada variante en cada sucursal
            $t->id();
            $t->foreignId('variante_sku_id')->constrained('variantes_sku')->cascadeOnDelete();
            $t->foreignId('sucursal_id')->constrained('sucursales')->cascadeOnDelete();
            $t->unsignedInteger('cantidad')->default(0);
            $t->unsignedInteger('stock_minimo')->default(0);
            $t->timestamps();
            $t->unique(['variante_sku_id', 'sucursal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('existencias');
    }
};
