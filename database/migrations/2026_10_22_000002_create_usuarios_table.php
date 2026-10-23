<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $t) {
            $t->id();
            $t->string('nombre', 120);
            $t->string('email')->unique();
            $t->string('password');
            $t->boolean('activo')->default(true);
            $t->timestamp('email_verificado_en')->nullable();
            $t->rememberToken();
            $t->timestamps();
            $t->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
