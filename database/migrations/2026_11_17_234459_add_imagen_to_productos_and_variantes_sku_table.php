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
        if (Schema::hasTable('productos') && ! Schema::hasColumn('productos', 'imagen')) {
            Schema::table('productos', function (Blueprint $table): void {
                $table->string('imagen', 500)->nullable()->after('descripcion');
            });
        }

        if (Schema::hasTable('variantes_sku') && ! Schema::hasColumn('variantes_sku', 'imagen')) {
            Schema::table('variantes_sku', function (Blueprint $table): void {
                $table->string('imagen', 500)->nullable()->after('descripcion');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('productos') && Schema::hasColumn('productos', 'imagen')) {
            Schema::table('productos', function (Blueprint $table): void {
                $table->dropColumn('imagen');
            });
        }

        if (Schema::hasTable('variantes_sku') && Schema::hasColumn('variantes_sku', 'imagen')) {
            Schema::table('variantes_sku', function (Blueprint $table): void {
                $table->dropColumn('imagen');
            });
        }
    }
};
