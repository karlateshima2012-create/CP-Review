<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use App\Models\BotScript;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BotScriptTest extends TestCase
{
    use RefreshDatabase;

    private User $lojista;
    private Cliente $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->lojista = User::create([
            'name' => 'Lojista Test',
            'email' => 'lojista@test.com',
            'password' => Hash::make('password123'),
            'role' => 'owner'
        ]);

        $this->tenant = Cliente::create([
            'user_id' => $this->lojista->id,
            'nome_empresa' => 'Loja Teste',
            'email' => 'loja@test.com',
            'slug' => 'loja-teste',
            'pais' => 'br',
            'canal_notificacao' => 'whatsapp',
            'plano' => 'standard',
            'ativo' => true,
            'data_ativacao' => now()
        ]);

        $this->lojista->update(['tenant_id' => $this->tenant->id]);
    }

    public function test_api_retorna_valores_padrao_se_sem_script_salvo(): void
    {
        $response = $this->get("/api/bot-script/loja-teste");

        $response->assertStatus(200);
        $response->assertJsonPath('lang.welcome.text', 'Como foi sua experiência hoje?');
        $response->assertJsonPath('lang.welcome.step', 1);
        $response->assertJsonPath('lang.q_first_visit.step', null);
    }

    public function test_lojista_pode_atualizar_configuracao_do_bot_com_etapa_nula(): void
    {
        $payload = [
            'messages' => [
                'pt' => [
                    'welcome' => ['text' => 'Olá customizado', 'step' => '1'],
                    'q_first_visit' => ['text' => 'Primeira vez?', 'step' => ''], // empty sequence step to hide it
                    'first_visit_ack' => ['text' => 'Legal!', 'step' => '2'],
                    'askRate' => ['text' => 'Diga uma nota', 'step' => '3'],
                    'highRate' => ['text' => 'Excelente!', 'step' => '4'],
                    'q_period' => ['text' => 'Horario?', 'step' => '5'],
                    'q_recommend' => ['text' => 'Recomenda?', 'step' => '6'],
                    'recommend_yes' => ['text' => 'Google link', 'step' => '7'],
                    'highFinalMsg' => ['text' => 'Fim', 'step' => '8'],
                    'lowRate' => ['text' => 'Lamentamos', 'step' => '4'],
                    'lowRateQ' => ['text' => 'Motivo?', 'step' => '5'],
                    'q_optional_text' => ['text' => 'Comente', 'step' => '6'],
                    'q_optional_photo' => ['text' => 'Foto', 'step' => '7'],
                    'photo_ack' => ['text' => 'Valeu foto', 'step' => '8'],
                    'q_contact' => ['text' => 'Contato', 'step' => '9'],
                    'lowFinalMsg' => ['text' => 'Fim negativo', 'step' => '10'],
                ],
                'ja' => [
                    'welcome' => ['text' => 'Konnichiwa', 'step' => '1'],
                    'q_first_visit' => ['text' => 'First?', 'step' => '2'],
                    'first_visit_ack' => ['text' => 'Ack', 'step' => '3'],
                    'askRate' => ['text' => 'Rate', 'step' => '4'],
                    'highRate' => ['text' => 'High', 'step' => '5'],
                    'q_period' => ['text' => 'Period', 'step' => '6'],
                    'q_recommend' => ['text' => 'Rec', 'step' => '7'],
                    'recommend_yes' => ['text' => 'Google', 'step' => '8'],
                    'highFinalMsg' => ['text' => 'End', 'step' => '9'],
                    'lowRate' => ['text' => 'Low', 'step' => '5'],
                    'lowRateQ' => ['text' => 'Why', 'step' => '6'],
                    'q_optional_text' => ['text' => 'Text', 'step' => '7'],
                    'q_optional_photo' => ['text' => 'Photo', 'step' => '8'],
                    'photo_ack' => ['text' => 'Ack photo', 'step' => '9'],
                    'q_contact' => ['text' => 'Contact', 'step' => '10'],
                    'lowFinalMsg' => ['text' => 'End low', 'step' => '11'],
                ]
            ]
        ];

        $response = $this->actingAs($this->lojista)
            ->post(route('cliente.perfil.update', $this->tenant->id), $payload);

        $response->assertRedirect();
        
        // Check database bot_scripts
        $this->assertDatabaseHas('bot_scripts', [
            'tenant_id' => $this->tenant->id,
            'locale' => 'pt',
        ]);

        // Access api script and check that welcome is custom and q_first_visit is null (skipped)
        $apiResponse = $this->get("/api/bot-script/loja-teste");
        $apiResponse->assertStatus(200);
        $apiResponse->assertJsonPath('lang.welcome.text', 'Olá customizado');
        $apiResponse->assertJsonPath('lang.welcome.step', 1);
        $apiResponse->assertJsonPath('lang.q_first_visit.step', null);
        $apiResponse->assertJsonPath('lang.q_first_visit.text', 'Primeira vez?');
    }
}
