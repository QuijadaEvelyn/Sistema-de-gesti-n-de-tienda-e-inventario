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
            if (! Schema::hasColumn('devoluciones', 'folio_devolucion')) {
                $table->string('folio_devolucion', 40)->nullable()->unique()->after('id');
            }
            if (! Schema::hasColumn('devoluciones', 'etapa_devolucion')) {
                $table->string('etapa_devolucion', 40)->default('solicitada')->after('estatus')->index();
            }
            if (! Schema::hasColumn('devoluciones', 'comentarios_cliente')) {
                $table->text('comentarios_cliente')->nullable()->after('motivo');
            }
            if (! Schema::hasColumn('devoluciones', 'evidencias')) {
                $table->json('evidencias')->nullable()->after('comentarios_cliente');
            }
            if (! Schema::hasColumn('devoluciones', 'dictamen_admin')) {
                $table->text('dictamen_admin')->nullable()->after('monto_reembolso');
            }
            if (! Schema::hasColumn('devoluciones', 'guia_retorno')) {
                $table->string('guia_retorno', 50)->nullable()->after('dictamen_admin');
            }
            if (! Schema::hasColumn('devoluciones', 'instrucciones_retorno')) {
                $table->text('instrucciones_retorno')->nullable()->after('guia_retorno');
            }
            if (! Schema::hasColumn('devoluciones', 'destino_inventario')) {
                $table->string('destino_inventario', 40)->nullable()->after('instrucciones_retorno');
            }
            if (! Schema::hasColumn('devoluciones', 'sucursal_destino_id')) {
                $table->foreignId('sucursal_destino_id')->nullable()->after('destino_inventario')->constrained('sucursales')->nullOnDelete();
            }
            if (! Schema::hasColumn('devoluciones', 'nueva_graduacion')) {
                $table->string('nueva_graduacion', 120)->nullable()->after('sucursal_destino_id');
            }
            if (! Schema::hasColumn('devoluciones', 'stock_procesado')) {
                $table->boolean('stock_procesado')->default(false)->after('nueva_graduacion');
            }
            if (! Schema::hasColumn('devoluciones', 'revisado_por')) {
                $table->foreignId('revisado_por')->nullable()->after('stock_procesado')->constrained('usuarios')->nullOnDelete();
            }
            if (! Schema::hasColumn('devoluciones', 'aprobada_en')) {
                $table->timestamp('aprobada_en')->nullable()->after('revisado_por');
            }
            if (! Schema::hasColumn('devoluciones', 'recibida_en')) {
                $table->timestamp('recibida_en')->nullable()->after('aprobada_en');
            }
            if (! Schema::hasColumn('devoluciones', 'reembolsada_en')) {
                $table->timestamp('reembolsada_en')->nullable()->after('recibida_en');
            }
        });
    }

    public function down(): void
    {
        Schema::table('devoluciones', function (Blueprint $table): void {
            if (Schema::hasColumn('devoluciones', 'sucursal_destino_id')) {
                $table->dropConstrainedForeignId('sucursal_destino_id');
            }
            if (Schema::hasColumn('devoluciones', 'revisado_por')) {
                $table->dropConstrainedForeignId('revisado_por');
            }
            $table->dropColumn(array_filter([
                Schema::hasColumn('devoluciones', 'folio_devolucion') ? 'folio_devolucion' : null,
                Schema::hasColumn('devoluciones', 'etapa_devolucion') ? 'etapa_devolucion' : null,
                Schema::hasColumn('devoluciones', 'comentarios_cliente') ? 'comentarios_cliente' : null,
                Schema::hasColumn('devoluciones', 'evidencias') ? 'evidencias' : null,
                Schema::hasColumn('devoluciones', 'dictamen_admin') ? 'dictamen_admin' : null,
                Schema::hasColumn('devoluciones', 'guia_retorno') ? 'guia_retorno' : null,
                Schema::hasColumn('devoluciones', 'instrucciones_retorno') ? 'instrucciones_retorno' : null,
                Schema::hasColumn('devoluciones', 'destino_inventario') ? 'destino_inventario' : null,
                Schema::hasColumn('devoluciones', 'nueva_graduacion') ? 'nueva_graduacion' : null,
                Schema::hasColumn('devoluciones', 'stock_procesado') ? 'stock_procesado' : null,
                Schema::hasColumn('devoluciones', 'aprobada_en') ? 'aprobada_en' : null,
                Schema::hasColumn('devoluciones', 'recibida_en') ? 'recibida_en' : null,
                Schema::hasColumn('devoluciones', 'reembolsada_en') ? 'reembolsada_en' : null,
            ]));
        });
    }
};
