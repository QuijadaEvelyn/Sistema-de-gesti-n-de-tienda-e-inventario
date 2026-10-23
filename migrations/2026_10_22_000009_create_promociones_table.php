<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promociones', function (Blueprint $t) {
            $t->id();
            $t->string('nombre', 120);
            $t->text('descripcion')->nullable();
            $t->enum('tipo_descuento', ['porcentaje', 'monto'])->default('porcentaje');
            $t->decimal('valor', 10, 2);
            $t->date('inicia_en');
            $t->date('termina_en')->nullable();
            $t->boolean('activa')->default(true);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promociones');
    }
};
