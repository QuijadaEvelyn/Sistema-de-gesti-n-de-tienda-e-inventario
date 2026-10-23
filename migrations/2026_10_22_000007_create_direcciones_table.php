<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('direcciones', function (Blueprint $t) {
            $t->id();
            $t->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $t->string('alias', 50)->nullable();
            $t->string('calle');
            $t->string('numero_exterior', 20);
            $t->string('numero_interior', 20)->nullable();
            $t->string('colonia', 120);
            $t->string('municipio', 120);
            $t->string('estado', 80);
            $t->string('codigo_postal', 10);
            $t->string('pais', 60)->default('México');
            $t->string('referencias')->nullable();
            $t->boolean('predeterminada')->default(false);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('direcciones');
    }
};
