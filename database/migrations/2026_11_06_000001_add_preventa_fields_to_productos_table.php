<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $t): void {
            if (! Schema::hasColumn('productos', 'es_preventa')) {
                $t->boolean('es_preventa')->default(false)->after('activo');
            }

            if (! Schema::hasColumn('productos', 'limite_preventa')) {
                $t->unsignedInteger('limite_preventa')->nullable()->after('es_preventa');
            }
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $t): void {
            $columnsToDrop = [];

            if (Schema::hasColumn('productos', 'limite_preventa')) {
                $columnsToDrop[] = 'limite_preventa';
            }

            if (Schema::hasColumn('productos', 'es_preventa')) {
                $columnsToDrop[] = 'es_preventa';
            }

            if (! empty($columnsToDrop)) {
                $t->dropColumn($columnsToDrop);
            }
        });
    }
};
