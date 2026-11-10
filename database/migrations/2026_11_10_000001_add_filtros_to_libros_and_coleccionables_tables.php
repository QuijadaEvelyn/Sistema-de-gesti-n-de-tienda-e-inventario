<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('libros', function (Blueprint $table): void {
            if (! Schema::hasColumn('libros', 'genero')) {
                $table->string('genero', 60)->nullable()->after('autor');
            }
        });

        Schema::table('coleccionables', function (Blueprint $table): void {
            if (! Schema::hasColumn('coleccionables', 'categoria')) {
                $table->string('categoria', 60)->nullable()->after('serie');
            }
        });
    }

    public function down(): void
    {
        Schema::table('libros', function (Blueprint $table): void {
            if (Schema::hasColumn('libros', 'genero')) {
                $table->dropColumn('genero');
            }
        });

        Schema::table('coleccionables', function (Blueprint $table): void {
            if (Schema::hasColumn('coleccionables', 'categoria')) {
                $table->dropColumn('categoria');
            }
        });
    }
};
