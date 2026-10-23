<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario_rol', function (Blueprint $t) {
            $t->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $t->foreignId('rol_id')->constrained('roles')->cascadeOnDelete();
            $t->timestamps();
            $t->primary(['usuario_id', 'rol_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario_rol');
    }
};
