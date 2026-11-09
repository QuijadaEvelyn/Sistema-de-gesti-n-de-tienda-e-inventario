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
        if (Schema::hasTable('promociones')) {
            Schema::table('promociones', function (Blueprint $table): void {
                if (! Schema::hasColumn('promociones', 'producto_id')) {
                    $table->foreignId('producto_id')
                        ->nullable()
                        ->after('id')
                        ->constrained('productos')
                        ->nullOnDelete();
                }

                if (! Schema::hasColumn('promociones', 'es_preventa_exclusiva')) {
                    $table->boolean('es_preventa_exclusiva')
                        ->default(true)
                        ->after('valor');
                }

                if (! Schema::hasColumn('promociones', 'preventa_inicia_en')) {
                    $table->timestamp('preventa_inicia_en')
                        ->nullable()
                        ->after('es_preventa_exclusiva');
                }

                if (! Schema::hasColumn('promociones', 'apertura_publica_en')) {
                    $table->timestamp('apertura_publica_en')
                        ->nullable()
                        ->after('preventa_inicia_en');
                }

                if (! Schema::hasColumn('promociones', 'badge_etiqueta')) {
                    $table->string('badge_etiqueta', 120)
                        ->nullable()
                        ->after('apertura_publica_en');
                }
            });
        }

        if (Schema::hasTable('productos')) {
            Schema::table('productos', function (Blueprint $table): void {
                if (! Schema::hasColumn('productos', 'es_preventa')) {
                    $table->boolean('es_preventa')->default(false)->after('activo');
                }

                if (! Schema::hasColumn('productos', 'limite_preventa')) {
                    $table->unsignedInteger('limite_preventa')->nullable()->after('es_preventa');
                }

                if (! Schema::hasColumn('productos', 'preventa_exclusiva_hasta')) {
                    $table->timestamp('preventa_exclusiva_hasta')->nullable()->after('limite_preventa');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('promociones')) {
            Schema::table('promociones', function (Blueprint $table): void {
                $columnas = [];
                foreach (['producto_id', 'es_preventa_exclusiva', 'preventa_inicia_en', 'apertura_publica_en', 'badge_etiqueta'] as $columna) {
                    if (Schema::hasColumn('promociones', $columna)) {
                        $columnas[] = $columna;
                    }
                }

                if (in_array('producto_id', $columnas, true)) {
                    $table->dropConstrainedForeignId('producto_id');
                    $columnas = array_values(array_diff($columnas, ['producto_id']));
                }

                if (! empty($columnas)) {
                    $table->dropColumn($columnas);
                }
            });
        }

        if (Schema::hasTable('productos')) {
            Schema::table('productos', function (Blueprint $table): void {
                if (Schema::hasColumn('productos', 'preventa_exclusiva_hasta')) {
                    $table->dropColumn('preventa_exclusiva_hasta');
                }
            });
        }
    }
};

