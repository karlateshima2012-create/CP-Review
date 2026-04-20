<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('clientes')) {
            Schema::table('clientes', function (Blueprint $table) {
                if (!Schema::hasColumn('clientes', 'valor_mensal')) {
                    $table->decimal('valor_mensal', 10, 2)->default(0)->after('plano');
                }
                if (!Schema::hasColumn('clientes', 'trial_ends_at')) {
                    $table->timestamp('trial_ends_at')->nullable()->after('valor_mensal');
                }
                if (!Schema::hasColumn('clientes', 'status')) {
                    $table->string('status')->default('ativo')->after('trial_ends_at'); // ativo, trial, inativo
                }
            });
        }
    }

    public function down(): void {
        if (Schema::hasTable('clientes')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->dropColumn(['valor_mensal', 'trial_ends_at', 'status']);
            });
        }
    }
};
