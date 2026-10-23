<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_pagos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('pago_id')->constrained('pagos')->cascadeOnDelete();
            $t->string('referencia_externa')->nullable(); // id de pasarela / folio bancario
            $t->string('ultimos_digitos', 4)->nullable();
            $t->string('autorizacion', 60)->nullable();
            $t->json('respuesta_pasarela')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_pagos');
    }
};
