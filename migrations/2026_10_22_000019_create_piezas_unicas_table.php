<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('piezas_unicas', function (Blueprint $t) {
            // Ejemplares identificables individualmente (numeración, certificado, etc.)
            $t->id();
            $t->foreignId('existencia_id')->constrained('existencias')->cascadeOnDelete();
            $t->string('numero_serie', 80)->unique();
            $t->string('certificado')->nullable();
            $t->text('notas')->nullable();
            $t->enum('estado', ['disponible', 'reservada', 'vendida'])->default('disponible');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('piezas_unicas');
    }
};
