<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Administrador Global
        \App\Models\User::factory()->create([
            'name' => 'Admin CP Review',
            'email' => 'admin@cpreview.com',
            'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
        ]);

        // Lojista de Teste
        $user = \App\Models\User::factory()->create([
            'name' => 'Lojista de Teste',
            'email' => 'loja@teste.com',
            'password' => \Illuminate\Support\Facades\Hash::make('loja123'),
        ]);

        \App\Models\Cliente::create([
            'user_id' => $user->id,
            'nome_empresa' => 'Restaurante Sabor Senior',
            'email' => 'contato@saborsenior.com',
            'slug' => 'sabor-senior',
            'telefone_whatsapp' => '5511999999999',
            'plano' => 'elite',
            'ativo' => true,
            'data_ativacao' => now(),
        ]);

        // Lojista de Teste Japão
        $userJp = \App\Models\User::factory()->create([
            'name' => 'Tanaka-san',
            'email' => 'tanaka@test.jp',
            'password' => \Illuminate\Support\Facades\Hash::make('tanaka123'),
        ]);

        \App\Models\Cliente::create([
            'user_id' => $userJp->id,
            'nome_empresa' => '田中寿司 (Tanaka Sushi)',
            'slug' => 'tanaka-sushi',
            'pais' => 'jp',
            'canal_notificacao' => 'line',
            'line_user_id' => 'U1234567890abcdef',
            'msg_boas_vindas_jp' => 'ご来店ありがとうございます。',
            'msg_pergunta_nota_jp' => '本日の体験はいかがでしたでしょうか？',
            'msg_agradecimento_alta_jp' => '高評価をいただき、誠にありがとうございます。励みになります！',
            'msg_agradecimento_baixa_jp' => '貴重なご意見をいただき、ありがとうございます。改善に努めてまいります。',
            'plano' => 'elite',
            'ativo' => true,
        ]);
    }
}
