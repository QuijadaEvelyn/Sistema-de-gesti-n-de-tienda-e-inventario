<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('pedido_id')->constrained('pedidos')->restrictOnDelete();
            $t->enum('metodo', ['tarjeta', 'transferencia', 'efectivo', 'paypal', 'otro']);
            $t->decimal('monto', 12, 2);
            $t->enum('estatus', ['pendiente', 'aprobado', 'rechazado', 'reembolsado'])->default('pendiente');
            $t->timestamp('pagado_en')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
