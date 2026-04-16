<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transacoes', function (Blueprint $table) {
            $table->id();
            $table->string('transacao_id')->unique();
            $table->string('empresa');
            $table->string('email');
            $table->string('telefone', 20);
            $table->string('line_id')->nullable();
            $table->string('plano');
            $table->decimal('valor', 10, 2);
            $table->string('slug')->nullable();
            $table->enum('pais', ['br', 'jp'])->default('br');
            $table->enum('canal', ['email', 'whatsapp', 'line'])->default('email');
            $table->foreignId('cliente_id')->nullable()->constrained();
            $table->enum('status', ['pendente', 'aprovado', 'rejeitado'])->default('pendente');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transacoes');
    }
};
