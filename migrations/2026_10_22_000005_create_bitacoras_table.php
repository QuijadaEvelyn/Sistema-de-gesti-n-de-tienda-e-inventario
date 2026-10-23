<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bitacoras', function (Blueprint $t) {
            $t->id();
            $t->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $t->string('accion', 80);
            $t->string('tabla_afectada', 80)->nullable();
            $t->unsignedBigInteger('registro_id')->nullable();
            $t->json('detalle')->nullable();
            $t->string('ip', 45)->nullable();
            $t->timestamp('created_at')->useCurrent();
            $t->index(['tabla_afectada', 'registro_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacoras');
    }
};
