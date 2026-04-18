<?php

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
        // Renaming cliente_id to tenant_id in avaliacoes
        if (Schema::hasColumn('avaliacoes', 'cliente_id')) {
            Schema::table('avaliacoes', function (Blueprint $table) {
                // Rename first
                $table->renameColumn('cliente_id', 'tenant_id');
            });
        }

        // Renaming cliente_id to tenant_id in transacoes
        if (Schema::hasColumn('transacoes', 'cliente_id')) {
            Schema::table('transacoes', function (Blueprint $table) {
                $table->renameColumn('cliente_id', 'tenant_id');
            });
        }

        // Adding Scalability Indexes
        Schema::table('avaliacoes', function (Blueprint $table) {
            $table->index(['tenant_id', 'created_at'], 'idx_tenant_created_at');
            $table->index(['tenant_id', 'resolvido'], 'idx_tenant_resolvido');
            $table->index(['tenant_id', 'nota'], 'idx_tenant_nota');
        });

        Schema::table('transacoes', function (Blueprint $table) {
            $table->index('tenant_id', 'idx_tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('avaliacoes', function (Blueprint $table) {
            $table->dropIndex('idx_tenant_created_at');
            $table->dropIndex('idx_tenant_resolvido');
            $table->dropIndex('idx_tenant_nota');
            $table->renameColumn('tenant_id', 'cliente_id');
        });

        Schema::table('transacoes', function (Blueprint $table) {
            $table->dropIndex('idx_tenant_id');
            $table->renameColumn('tenant_id', 'cliente_id');
        });
    }
};
