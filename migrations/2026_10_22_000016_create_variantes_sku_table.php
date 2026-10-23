<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variantes_sku', function (Blueprint $t) {
            $t->id();
            $t->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $t->string('sku', 60)->unique();
            $t->string('descripcion')->nullable(); // ej. "Tapa dura", "Vinilo rojo"
            $t->decimal('precio', 10, 2)->nullable(); // si es null, usa productos.precio
            $t->boolean('activa')->default(true);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variantes_sku');
    }
};
