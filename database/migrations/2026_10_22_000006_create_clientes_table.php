<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $t) {
            $t->id();
            // "Tiene acceso como": un cliente puede (o no) tener cuenta de usuario
            $t->foreignId('usuario_id')->nullable()->unique()->constrained('usuarios')->nullOnDelete();
            $t->string('nombre', 120);
            $t->string('apellidos', 120);
            $t->string('email')->unique();
            $t->string('telefono', 20)->nullable();
            $t->timestamps();
            $t->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
