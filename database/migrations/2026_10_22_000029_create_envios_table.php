<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('envios', function (Blueprint $t) {
            $t->id();
            $t->foreignId('pedido_id')->constrained('pedidos')->restrictOnDelete();
            $t->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete(); // origen
            $t->string('paqueteria', 80)->nullable();
            $t->string('numero_guia', 80)->nullable()->unique();
            $t->enum('estatus', ['preparando', 'en_transito', 'entregado', 'devuelto'])->default('preparando');
            $t->timestamp('enviado_en')->nullable();
            $t->timestamp('entregado_en')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('envios');
    }
};
