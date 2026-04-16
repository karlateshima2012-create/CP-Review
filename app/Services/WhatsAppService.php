<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $url;
    protected string $instance;
    protected string $apiKey;

    public function __construct()
    {
        $this->url = config('services.evolution.url');
        $this->instance = config('services.evolution.instance');
        $this->apiKey = config('services.evolution.api_key');
    }

    public function sendMessage(string $phone, string $message): bool
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post("{$this->url}/message/sendText/{$this->instance}", [
                'number' => $this->formatPhone($phone),
                'text' => $message
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('WhatsApp send error: ' . $e->getMessage());
            return false;
        }
    }

    public function formatPhone(string $phone): string
    {
        // Remove tudo que não é número
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Adiciona código do país se não tiver
        if (strlen($phone) === 11) {
            $phone = '55' . $phone;
        }
        
        return $phone;
    }

    public function sendNegativeNotification($cliente, $avaliacao): bool
    {
        $message = "⚠️ *NOVA AVALIAÇÃO BAIXA* ⚠️\n\n";
        $message .= "Empresa: *{$cliente->nome_empresa}*\n";
        $message .= "Nota: " . str_repeat("⭐", $avaliacao->nota) . " ({$avaliacao->nota}/5)\n\n";
        $message .= "Feedback: \"{$avaliacao->feedback}\"\n\n";
        $message .= "🔗 Ver todas: " . url("/cliente/{$cliente->id}");

        return $this->sendMessage($cliente->telefone_whatsapp, $message);
    }

    public function sendResponseToCustomer($avaliacao): bool
    {
        $cliente = $avaliacao->cliente;
        
        // Texto padrão de encerramento (Foco em: ouvido, acatado e corrigido)
        $message = "Olá! Aqui é a equipe do *{$cliente->nome_empresa}*.\n\n";
        $message .= "Gostaríamos de informar que sua mensagem foi ouvida e acatada pela nossa gerência.\n";
        $message .= "Já tomamos as providências necessárias para corrigir o ponto que você nos sinalizou. Sua colaboração foi fundamental para melhorarmos nosso serviço.\n\n";
        
        // Se o lojista escreveu uma mensagem personalizada/cupom, ela é anexada
        if ($avaliacao->resposta_dono) {
            $message .= "Mensagem da gerência: \"{$avaliacao->resposta_dono}\"\n\n";
        }

        $message .= "Pedimos desculpas pelo transtorno e esperamos vê-lo em breve!";

        // Envia para o contato (WhatsApp) que o cliente deixou na avaliação
        return $this->sendMessage($avaliacao->contato_valor, $message);
    }
}
