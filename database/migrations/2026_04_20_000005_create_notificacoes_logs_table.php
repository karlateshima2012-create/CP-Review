<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('notificacoes_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignUuid('avaliacao_id')->nullable()->constrained('avaliacoes')->onDelete('set null');
            $table->string('canal'); // whatsapp, line, email
            $table->string('destinatario');
            $table->text('mensagem');
            $table->string('status')->default('enviada'); // enviada, falha, pendente
            $table->text('erro_mensagem')->nullable();
            $table->integer('retries')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('notificacoes_logs');
    }
};
