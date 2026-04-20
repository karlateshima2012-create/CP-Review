<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('clientes')) {
            Schema::table('clientes', function (Blueprint $table) {
                if (!Schema::hasColumn('clientes', 'qr_logo_path')) {
                    $table->string('qr_logo_path')->nullable();
                }
                if (!Schema::hasColumn('clientes', 'qr_color')) {
                    $table->string('qr_color', 7)->default('#7C3AED');
                }
            });
        }
    }

    public function down(): void {
        if (Schema::hasTable('clientes')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->dropColumn(['qr_logo_path', 'qr_color']);
            });
        }
    }
};
