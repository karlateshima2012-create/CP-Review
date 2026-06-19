<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->enum('pack_idioma', ['pt_ja', 'ja_en'])->default('pt_ja')->after('pais');
        });

        if (Schema::hasTable('transacoes')) {
            Schema::table('transacoes', function (Blueprint $table) {
                $table->enum('pack_idioma', ['pt_ja', 'ja_en'])->default('pt_ja')->after('pais');
            });
        }
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn('pack_idioma');
        });

        if (Schema::hasTable('transacoes') && Schema::hasColumn('transacoes', 'pack_idioma')) {
            Schema::table('transacoes', function (Blueprint $table) {
                $table->dropColumn('pack_idioma');
            });
        }
    }
};
