<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use App\Models\Avaliacao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    private User $lojistaA;
    private User $lojistaB;
    private Cliente $tenantA;
    private Cliente $tenantB;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Criar Usuário A e Tenant A
        $this->lojistaA = User::create([
            'name' => 'Lojista A',
            'email' => 'lojistaA@cpreview.com',
            'password' => Hash::make('password123'),
            'role' => 'owner'
        ]);

        $this->tenantA = Cliente::create([
            'user_id' => $this->lojistaA->id,
            'nome_empresa' => 'Loja A',
            'email' => 'lojaA@cpreview.com',
            'slug' => 'loja-a',
            'telefone_whatsapp' => '5511999999999',
            'pais' => 'br',
            'canal_notificacao' => 'whatsapp',
            'plano' => 'standard',
            'ativo' => true,
            'data_ativacao' => now()
        ]);

        $this->lojistaA->update(['tenant_id' => $this->tenantA->id]);

        // 2. Criar Usuário B e Tenant B
        $this->lojistaB = User::create([
            'name' => 'Lojista B',
            'email' => 'lojistaB@cpreview.com',
            'password' => Hash::make('password123'),
            'role' => 'owner'
        ]);

        $this->tenantB = Cliente::create([
            'user_id' => $this->lojistaB->id,
            'nome_empresa' => 'Loja B',
            'email' => 'lojaB@cpreview.com',
            'slug' => 'loja-b',
            'telefone_whatsapp' => '5511999999998',
            'pais' => 'br',
            'canal_notificacao' => 'whatsapp',
            'plano' => 'standard',
            'ativo' => true,
            'data_ativacao' => now()
        ]);

        $this->lojistaB->update(['tenant_id' => $this->tenantB->id]);
    }

    public function test_lojista_pode_acessar_seu_proprio_dashboard(): void
    {
        $response = $this->actingAs($this->lojistaA)
            ->get(route('cliente.dashboard', $this->tenantA->id));

        $response->assertStatus(200);
        $response->assertSee('Loja A');
        $response->assertDontSee('Loja B');
    }

    public function test_lojista_nao_pode_acessar_dashboard_de_outro_lojista(): void
    {
        $response = $this->actingAs($this->lojistaA)
            ->get(route('cliente.dashboard', $this->tenantB->id));

        // Deve retornar 403 Forbidden através da Policy
        $response->assertStatus(403);
    }

    public function test_lojista_nao_pode_ver_avaliacoes_de_outro_lojista(): void
    {
        // Criar avaliação no Tenant B
        $avaliacaoB = Avaliacao::create([
            'tenant_id' => $this->tenantB->id,
            'nota' => 2,
            'feedback' => 'Feedback da Loja B',
            'nome_cliente' => 'Cliente Secreto B',
            'token_resposta' => 'token-b',
        ]);

        // Lojista A tenta listar as avaliações do Tenant B
        $response = $this->actingAs($this->lojistaA)
            ->get(route('cliente.avaliacoes', $this->tenantB->id));

        $response->assertStatus(403);
    }

    public function test_lojista_nao_pode_responder_avaliacao_de_outro_lojista(): void
    {
        // Criar avaliação no Tenant B
        $avaliacaoB = Avaliacao::create([
            'tenant_id' => $this->tenantB->id,
            'nota' => 1,
            'feedback' => 'Muito ruim Loja B',
            'nome_cliente' => 'Cliente Secreto B',
            'token_resposta' => 'token-b2',
        ]);

        // Lojista A tenta responder a avaliação da Loja B
        $response = $this->actingAs($this->lojistaA)
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class)
            ->post("/cliente/avaliacao/{$avaliacaoB->id}/responder", [
                'resposta' => 'Resposta do Lojista A invasor'
            ]);

        // Retorna 404 porque a avaliação pertence a outro tenant e o TenantScope oculta o registro do model binding
        $response->assertStatus(404);
        
        // Verifica se a avaliação não foi resolvida/respondida
        $this->assertFalse((bool)$avaliacaoB->fresh()->resolvido);
    }

    public function test_tenant_scope_isola_consultas_eloquent(): void
    {
        // Criar avaliações no Tenant A
        Avaliacao::create([
            'tenant_id' => $this->tenantA->id,
            'nota' => 5,
            'feedback' => 'Excelente Loja A',
            'nome_cliente' => 'Cliente A',
            'token_resposta' => 'token-a',
        ]);

        // Criar avaliações no Tenant B
        Avaliacao::create([
            'tenant_id' => $this->tenantB->id,
            'nota' => 1,
            'feedback' => 'Ruim Loja B',
            'nome_cliente' => 'Cliente B',
            'token_resposta' => 'token-b',
        ]);

        // Logar como Lojista A
        $this->actingAs($this->lojistaA);

        // A consulta global deve retornar apenas os dados do Tenant A por causa do escopo
        $avaliacoes = Avaliacao::all();

        $this->assertCount(1, $avaliacoes);
        $this->assertEquals($this->tenantA->id, $avaliacoes->first()->tenant_id);
        $this->assertEquals('Excelente Loja A', $avaliacoes->first()->feedback);
    }
}
