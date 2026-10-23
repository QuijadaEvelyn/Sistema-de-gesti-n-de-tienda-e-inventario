<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalogos', function (Blueprint $t) {
            $t->id();
            $t->string('nombre', 120)->unique();
            $t->enum('tipo', ['libro', 'disco', 'coleccionable']);
            $t->text('descripcion')->nullable();
            $t->boolean('activo')->default(true);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogos');
    }
};
