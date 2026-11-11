<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('coleccionables') && !Schema::hasColumn('coleccionables', 'imagen')) {
            Schema::table('coleccionables', function (Blueprint $table) {
                $table->string('imagen')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('coleccionables') && Schema::hasColumn('coleccionables', 'imagen')) {
            Schema::table('coleccionables', function (Blueprint $table) {
                $table->dropColumn('imagen');
            });
        }
    }
};

