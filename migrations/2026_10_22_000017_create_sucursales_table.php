<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sucursales', function (Blueprint $t) {
            $t->id();
            $t->string('nombre', 120)->unique();
            $t->string('direccion');
            $t->string('telefono', 20)->nullable();
            $t->boolean('activa')->default(true);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sucursales');
    }
};
