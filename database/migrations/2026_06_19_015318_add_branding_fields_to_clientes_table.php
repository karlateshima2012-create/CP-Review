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
        if (Schema::hasTable('clientes')) {
            Schema::table('clientes', function (Blueprint $table) {
                if (!Schema::hasColumn('clientes', 'logo_path')) {
                    $table->string('logo_path')->nullable();
                }
                if (!Schema::hasColumn('clientes', 'cover_path')) {
                    $table->string('cover_path')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('clientes')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->dropColumn(['logo_path', 'cover_path']);
            });
        }
    }
};
