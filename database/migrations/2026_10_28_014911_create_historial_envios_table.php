<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_envios', function (Blueprint $t) {
            $t->id();
            $t->foreignId('envio_id')->constrained('envios')->cascadeOnDelete();
            $t->string('estado', 60);
            $t->string('ubicacion', 120)->nullable();
            $t->text('descripcion')->nullable();
            $t->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_envios');
    }
};
