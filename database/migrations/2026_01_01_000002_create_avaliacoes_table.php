<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('avaliacoes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->tinyInteger('nota');
            $table->text('feedback')->nullable();
            $table->string('problema')->nullable();
            $table->string('nome_cliente')->default('Anônimo');
            $table->string('tipo_contato', 20)->nullable();
            $table->string('contato_valor')->nullable();
            $table->string('token_resposta')->nullable()->unique();
            $table->text('resposta_dono')->nullable();
            $table->timestamp('respondida_em')->nullable();
            
            // Dados de BI e Insights
            $table->boolean('primeira_visita')->default(false);
            $table->string('periodo_visita', 20)->nullable(); // almoço, jantar, café, etc
            $table->string('foto_problema')->nullable();
            
            $table->boolean('resolvido')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('avaliacoes');
    }
};
