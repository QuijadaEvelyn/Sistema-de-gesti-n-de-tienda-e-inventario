<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_envios', function (Blueprint $t) {
            $t->id();
            $t->foreignId('envio_id')->constrained('envios')->cascadeOnDelete();
            $t->foreignId('detalle_pedido_id')->constrained('detalle_pedidos')->restrictOnDelete();
            $t->unsignedInteger('cantidad');
            $t->timestamps();
            $t->unique(['envio_id', 'detalle_pedido_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_envios');
    }
};
