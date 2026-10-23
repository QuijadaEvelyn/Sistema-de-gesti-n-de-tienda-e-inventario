<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coleccionables', function (Blueprint $t) {
            $t->foreignId('producto_id')->primary()->constrained('productos')->cascadeOnDelete();
            $t->string('marca')->nullable();
            $t->string('serie')->nullable();
            $t->string('material', 80)->nullable();
            $t->boolean('edicion_limitada')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coleccionables');
    }
};
