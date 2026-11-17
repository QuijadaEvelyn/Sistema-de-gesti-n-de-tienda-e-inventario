<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mensajes_contacto', function (Blueprint $table): void {
            $table->id();
            $table->string('nombre', 150);
            $table->string('email', 150);
            $table->string('telefono', 50)->nullable();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->string('asunto', 200);
            $table->text('mensaje');
            $table->enum('estatus', ['pendiente', 'leido', 'respondido'])->default('pendiente');
            $table->string('ip', 45)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensajes_contacto');
    }
};
