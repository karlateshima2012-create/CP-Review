<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DevSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@cpreview.com'],
            [
                'name' => 'Admin CP Review',
                'password' => Hash::make('admin123'),
                'role' => 'super_admin',
            ]
        );

        // Lojista de teste — Creative Print (BR)
        $loja = User::updateOrCreate(
            ['email' => 'loja@teste.com'],
            [
                'name' => 'Lojista de Teste',
                'password' => Hash::make('loja123'),
                'role' => 'owner',
            ]
        );

        $creativeData = [
            'user_id' => $loja->id,
            'nome_empresa' => 'CREATIVE PRINT',
            'email' => 'contato@creativeprint.com',
            'slug' => 'creative-print',
            'telefone_whatsapp' => '5511999999999',
            'plano' => 'elite',
            'ativo' => true,
            'data_ativacao' => now(),
        ];

        if (Schema::hasColumn('clientes', 'google_maps_link')) {
            $creativeData['google_maps_link'] = 'https://g.page/r/CT0IMW6LPFnnEBM/review';
        }

        $clienteLoja = Cliente::updateOrCreate(
            ['slug' => 'creative-print'],
            $creativeData
        );

        $loja->update(['tenant_id' => $clienteLoja->id]);

        // Lojista de teste — Tanaka Sushi (JP)
        $tanaka = User::updateOrCreate(
            ['email' => 'tanaka@test.jp'],
            [
                'name' => 'Tanaka-san',
                'password' => Hash::make('tanaka123'),
                'role' => 'owner',
            ]
        );

        $clienteTanaka = Cliente::where('slug', 'tanaka-sushi')->first();
        if ($clienteTanaka) {
            $clienteTanaka->update(['user_id' => $tanaka->id]);
            $tanaka->update(['tenant_id' => $clienteTanaka->id]);
        }
    }
}
