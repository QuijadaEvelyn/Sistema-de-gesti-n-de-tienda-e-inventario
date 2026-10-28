<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $t) {
            if (! Schema::hasColumn('pagos', 'metodo_pago')) {
                $t->enum('metodo_pago', ['oxxo', 'transferencia', 'tarjeta'])->nullable()->after('metodo');
            }

            if (! Schema::hasColumn('pagos', 'codigo_oxxo')) {
                $t->string('codigo_oxxo', 60)->nullable()->after('metodo_pago');
            }

            if (! Schema::hasColumn('pagos', 'referencia_bancaria')) {
                $t->string('referencia_bancaria', 80)->nullable()->after('codigo_oxxo');
            }

            if (! Schema::hasColumn('pagos', 'expira_at')) {
                $t->timestamp('expira_at')->nullable()->after('referencia_bancaria');
            }

            if (! Schema::hasColumn('pagos', 'comprobante_url')) {
                $t->string('comprobante_url')->nullable()->after('expira_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $t) {
            $columnsToDrop = [];

            foreach (['metodo_pago', 'codigo_oxxo', 'referencia_bancaria', 'expira_at', 'comprobante_url'] as $col) {
                if (Schema::hasColumn('pagos', $col)) {
                    $columnsToDrop[] = $col;
                }
            }

            if (! empty($columnsToDrop)) {
                $t->dropColumn($columnsToDrop);
            }
        });
    }
};
