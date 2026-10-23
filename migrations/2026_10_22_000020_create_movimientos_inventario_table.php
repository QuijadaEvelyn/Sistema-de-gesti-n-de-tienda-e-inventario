<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $t) {
            $t->id();
            $t->foreignId('existencia_id')->constrained('existencias')->restrictOnDelete();
            $t->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $t->enum('tipo', ['entrada', 'salida', 'ajuste', 'traspaso', 'devolucion']);
            $t->integer('cantidad'); // positivo o negativo
            $t->string('motivo')->nullable();
            $t->nullableMorphs('referencia'); // pedido, devolución, etc.
            $t->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
