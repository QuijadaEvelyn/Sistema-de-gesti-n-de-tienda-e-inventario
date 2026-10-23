<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discos', function (Blueprint $t) {
            $t->foreignId('producto_id')->primary()->constrained('productos')->cascadeOnDelete();
            $t->string('artista');
            $t->string('sello')->nullable();
            $t->string('genero', 80)->nullable();
            $t->enum('formato', ['CD', 'vinilo', 'cassette', 'digital'])->default('CD');
            $t->unsignedSmallInteger('anio_lanzamiento')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discos');
    }
};
