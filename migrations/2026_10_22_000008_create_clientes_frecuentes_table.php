<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes_frecuentes', function (Blueprint $t) {
            // Especialización 1:1 de clientes
            $t->foreignId('cliente_id')->primary()->constrained('clientes')->cascadeOnDelete();
            $t->date('desde');
            $t->unsignedInteger('puntos')->default(0);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes_frecuentes');
    }
};
