<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carrito_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('carrito_id')->constrained('carritos')->cascadeOnDelete();
            $t->foreignId('variante_sku_id')->constrained('variantes_sku')->cascadeOnDelete();
            $t->unsignedInteger('cantidad')->default(1);
            $t->timestamps();
            $t->unique(['carrito_id', 'variante_sku_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carrito_items');
    }
};
