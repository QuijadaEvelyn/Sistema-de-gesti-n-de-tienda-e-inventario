<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservas_stock', function (Blueprint $t) {
            $t->id();
            $t->foreignId('existencia_id')->constrained('existencias')->cascadeOnDelete();
            $t->foreignId('pedido_id')->nullable()->constrained('pedidos')->cascadeOnDelete();
            $t->foreignId('carrito_id')->nullable()->constrained('carritos')->cascadeOnDelete();
            $t->unsignedInteger('cantidad');
            $t->timestamp('expira_en')->nullable();
            $t->enum('estado', ['activa', 'confirmada', 'liberada', 'expirada'])->default('activa');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas_stock');
    }
};
