<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_pedidos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            $t->foreignId('variante_sku_id')->constrained('variantes_sku')->restrictOnDelete();
            $t->unsignedInteger('cantidad');
            $t->decimal('precio_unitario', 10, 2); // precio congelado al momento de la compra
            $t->decimal('subtotal', 12, 2);
            $t->timestamps();
            $t->unique(['pedido_id', 'variante_sku_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_pedidos');
    }
};
