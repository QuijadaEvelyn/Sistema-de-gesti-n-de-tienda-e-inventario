<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recuperacion_contrasenas', function (Blueprint $t) {
            $t->id();
            $t->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $t->string('token_hash')->unique();
            $t->timestamp('expira_en');
            $t->timestamp('usado_en')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recuperacion_contrasenas');
    }
};
