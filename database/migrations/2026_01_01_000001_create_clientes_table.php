<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('nome_empresa');
            $table->string('email')->nullable();
            $table->string('telefone_whatsapp', 20)->nullable();
            $table->string('line_user_id', 50)->nullable();
            $table->string('slug')->unique();
            $table->enum('pais', ['br', 'jp'])->default('br');
            $table->enum('canal_notificacao', ['email', 'whatsapp', 'line'])->default('email');
            $table->string('plano')->default('standard');
            $table->boolean('ativo')->default(true);
            $table->timestamp('data_ativacao')->nullable();

            // Scripts em Português (BR)
            $table->string('msg_boas_vindas_br')->default('Olá! 👋 Vamos avaliar sua experiência?');
            $table->string('msg_pergunta_nota_br')->default('Como foi sua experiência hoje?');
            $table->string('msg_agradecimento_alta_br')->default('Que ótimo! Fico muito feliz! 🎉');
            $table->string('msg_agradecimento_baixa_br')->default('Lamento que sua experiência não tenha sido boa. Agradecemos sua honestidade. 🙏');

            // Scripts em Japonês (JP - Keigo)
            $table->string('msg_boas_vindas_jp')->default('ご来店ありがとうございます。');
            $table->string('msg_pergunta_nota_jp')->default('本日の体験はいかがでしたでしょうか？');
            $table->string('msg_agradecimento_alta_jp')->default('高評価をいただき、誠にありがとうございます。励みになります！');
            $table->string('msg_agradecimento_baixa_jp')->default('貴重なご意見をいただき、ありがとうございます。改善に努めてまいります。');
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clientes');
    }
};
