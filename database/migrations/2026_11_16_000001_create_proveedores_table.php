<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedores', function (Blueprint $table): void {
            $table->id();
            $table->string('nombre');
            $table->string('razon_social')->nullable();
            $table->string('rfc', 20)->nullable()->unique();
            $table->string('contacto_nombre')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono', 50)->nullable();
            $table->text('direccion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        if (Schema::hasTable('movimientos_inventario') && ! Schema::hasColumn('movimientos_inventario', 'proveedor_id')) {
            Schema::table('movimientos_inventario', function (Blueprint $table): void {
                $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('movimientos_inventario') && Schema::hasColumn('movimientos_inventario', 'proveedor_id')) {
            Schema::table('movimientos_inventario', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('proveedor_id');
            });
        }

        Schema::dropIfExists('proveedores');
    }
};
