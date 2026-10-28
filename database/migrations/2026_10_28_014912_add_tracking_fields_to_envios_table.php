<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('envios', function (Blueprint $t) {
            if (! Schema::hasColumn('envios', 'numero_guia')) {
                $t->string('numero_guia', 80)->nullable()->unique()->after('paqueteria');
            }

            if (! Schema::hasColumn('envios', 'estado_actual')) {
                $t->enum('estado_actual', [
                    'pendiente',
                    'en_preparacion',
                    'en_transito',
                    'en_camino',
                    'entregado',
                    'incidencia',
                ])->default('pendiente')->after('numero_guia');
            }

            if (! Schema::hasColumn('envios', 'fecha_estimada_entrega')) {
                $t->timestamp('fecha_estimada_entrega')->nullable()->after('estado_actual');
            }
        });
    }

    public function down(): void
    {
        Schema::table('envios', function (Blueprint $t) {
            $columnsToDrop = [];

            if (Schema::hasColumn('envios', 'fecha_estimada_entrega')) {
                $columnsToDrop[] = 'fecha_estimada_entrega';
            }

            if (Schema::hasColumn('envios', 'estado_actual')) {
                $columnsToDrop[] = 'estado_actual';
            }

            if (! empty($columnsToDrop)) {
                $t->dropColumn($columnsToDrop);
            }
        });
    }
};
