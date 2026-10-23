<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cliente_promocion', function (Blueprint $t) {
            $t->foreignId('cliente_frecuente_id')->constrained('clientes_frecuentes', 'cliente_id')->cascadeOnDelete();
            $t->foreignId('promocion_id')->constrained('promociones')->cascadeOnDelete();
            $t->timestamp('recibida_en')->useCurrent();
            $t->timestamp('usada_en')->nullable();
            $t->primary(['cliente_frecuente_id', 'promocion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cliente_promocion');
    }
};
