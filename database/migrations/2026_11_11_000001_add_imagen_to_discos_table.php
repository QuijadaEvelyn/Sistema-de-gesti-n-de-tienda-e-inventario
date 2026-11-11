<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('discos') && ! Schema::hasColumn('discos', 'imagen')) {
            Schema::table('discos', function (Blueprint $table) {
                $table->string('imagen')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('discos') && Schema::hasColumn('discos', 'imagen')) {
            Schema::table('discos', function (Blueprint $table) {
                $table->dropColumn('imagen');
            });
        }
    }
};

