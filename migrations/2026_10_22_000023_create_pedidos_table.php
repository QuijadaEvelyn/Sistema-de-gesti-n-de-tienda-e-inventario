<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $t) {
            $t->id();
            $t->string('folio', 30)->unique();
            $t->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $t->foreignId('direccion_envio_id')->nullable()->constrained('direcciones')->nullOnDelete();
            $t->enum('estatus', ['pendiente', 'pagado', 'enviado', 'entregado', 'cancelado'])->default('pendiente');
            $t->decimal('subtotal', 12, 2)->default(0);
            $t->decimal('descuento', 12, 2)->default(0);
            $t->decimal('total', 12, 2)->default(0);
            $t->timestamps();
            $t->index(['cliente_id', 'estatus']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
