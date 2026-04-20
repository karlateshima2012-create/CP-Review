<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('avaliacoes') && Schema::hasColumn('avaliacoes', 'cliente_id')) {
            Schema::table('avaliacoes', function (Blueprint $table) {
                $table->renameColumn('cliente_id', 'tenant_id');
            });
        }
    }

    public function down(): void {
        if (Schema::hasTable('avaliacoes') && Schema::hasColumn('avaliacoes', 'tenant_id')) {
            Schema::table('avaliacoes', function (Blueprint $table) {
                $table->renameColumn('tenant_id', 'cliente_id');
            });
        }
    }
};
