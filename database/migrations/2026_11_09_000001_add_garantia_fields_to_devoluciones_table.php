<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devoluciones', function (Blueprint $table): void {
            $table->string('folio_devolucion', 40)->nullable()->after('id')->index();
            $table->string('etapa_devolucion', 40)->default('solicitada')->after('estatus')->index();
            $table->text('comentarios_cliente')->nullable()->after('motivo');
            $table->json('evidencias')->nullable()->after('comentarios_cliente');
            $table->text('dictamen_admin')->nullable()->after('monto_reembolso');
            $table->string('guia_retorno', 50)->nullable()->after('dictamen_admin');
            $table->text('instrucciones_retorno')->nullable()->after('guia_retorno');
            $table->string('destino_inventario', 40)->nullable()->after('instrucciones_retorno');
            $table->foreignId('sucursal_destino_id')->nullable()->after('destino_inventario')->constrained('sucursales')->nullOnDelete();
            $table->string('nueva_graduacion', 120)->nullable()->after('sucursal_destino_id');
            $table->boolean('stock_procesado')->default(false)->after('nueva_graduacion');
            $table->foreignId('revisado_por')->nullable()->after('stock_procesado')->constrained('usuarios')->nullOnDelete();
            $table->timestamp('aprobada_en')->nullable()->after('revisado_por');
            $table->timestamp('recibida_en')->nullable()->after('aprobada_en');
            $table->timestamp('reembolsada_en')->nullable()->after('recibida_en');
        });
    }

    public function down(): void
    {
        Schema::table('devoluciones', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('sucursal_destino_id');
            $table->dropConstrainedForeignId('revisado_por');
            $table->dropColumn([
                'folio_devolucion',
                'etapa_devolucion',
                'comentarios_cliente',
                'evidencias',
                'dictamen_admin',
                'guia_retorno',
                'instrucciones_retorno',
                'destino_inventario',
                'nueva_graduacion',
                'stock_procesado',
                'aprobada_en',
                'recibida_en',
                'reembolsada_en',
            ]);
        });
    }
};

