<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bot_scripts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('clientes')->onDelete('cascade');
            $table->string('locale', 5)->default('pt');
            $table->json('messages'); // Store all customizable messages as JSON
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('bot_scripts');
    }
};
