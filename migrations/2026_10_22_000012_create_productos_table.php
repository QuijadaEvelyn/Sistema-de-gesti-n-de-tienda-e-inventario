<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('catalogo_id')->constrained('catalogos')->restrictOnDelete();
            $t->string('nombre');
            $t->text('descripcion')->nullable();
            $t->decimal('precio', 10, 2);
            $t->boolean('activo')->default(true);
            $t->timestamps();
            $t->softDeletes();
            $t->index('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
