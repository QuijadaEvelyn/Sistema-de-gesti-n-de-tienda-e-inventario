<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devoluciones', function (Blueprint $t) {
            $t->id();
            $t->foreignId('pedido_id')->constrained('pedidos')->restrictOnDelete();
            $t->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete(); // quien solicita
            $t->foreignId('detalle_pedido_id')->constrained('detalle_pedidos')->restrictOnDelete();
            $t->unsignedInteger('cantidad');
            $t->string('motivo');
            $t->enum('estatus', ['solicitada', 'aprobada', 'rechazada', 'completada'])->default('solicitada');
            $t->decimal('monto_reembolso', 12, 2)->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devoluciones');
    }
};
