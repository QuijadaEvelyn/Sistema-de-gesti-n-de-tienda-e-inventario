<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('libros', function (Blueprint $t) {
            $t->foreignId('producto_id')->primary()->constrained('productos')->cascadeOnDelete();
            $t->string('isbn', 17)->unique()->nullable();
            $t->string('autor');
            $t->string('editorial')->nullable();
            $t->unsignedSmallInteger('anio_publicacion')->nullable();
            $t->unsignedSmallInteger('paginas')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('libros');
    }
};
