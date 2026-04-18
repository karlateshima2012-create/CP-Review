<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Avaliacao;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected string $waUrl;
    protected string $waInstance;
    protected string $waApiKey;
    protected string $lineToken;

    public function __construct()
    {
        $this->waUrl = config('services.evolution.url', '');
        $this->waInstance = config('services.evolution.instance', '');
        $this->waApiKey = config('services.evolution.api_key', '');
        $this->lineToken = config('services.line.channel_token', '');
    }

    /**
     * Envia notificação de avaliação baixa para o lojista
     */
    public function notifyLowRating(Cliente $cliente, Avaliacao $avaliacao): bool
    {
        $message = $this->buildNotificationMessage($cliente, $avaliacao);

        if ($cliente->canal_notificacao === 'whatsapp' && $cliente->telefone_whatsapp) {
            return $this->sendWhatsApp($cliente->telefone_whatsapp, $message);
        }

        if ($cliente->canal_notificacao === 'line' && $cliente->line_user_id) {
            return $this->sendLine($cliente->line_user_id, $message);
        }

        return false;
    }

    /**
     * Fecha o loop com o cliente (notifica que o feedback foi resolvido)
     */
    public function closeLoop(Avaliacao $avaliacao): bool
    {
        $cliente = $avaliacao->tenant;
        $message = $this->buildCloseLoopMessage($cliente, $avaliacao);

        if ($avaliacao->tipo_contato === 'whatsapp' && $avaliacao->contato_valor) {
            return $this->sendWhatsApp($avaliacao->contato_valor, $message);
        }

        // LINE customer response logic could be added here if LINE IDs are captured

        return false;
    }

    protected function buildCloseLoopMessage(Cliente $cliente, Avaliacao $avaliacao): string
    {
        if ($cliente->pais === 'jp') {
            $message = "【{$cliente->nome_empresa}】様より返信が届きました。\n\n";
            $message .= "フィードバックいただき、誠にありがとうございました。\n";
            $message .= "ご指摘いただいた点は改善に努めさせていただきます。\n";
            if ($avaliacao->resposta_dono) {
                $message .= "\n担当者からのメッセージ: \"{$avaliacao->resposta_dono}\"";
            }
            return $message;
        }

        $message = "Olá! Aqui é a equipe do *{$cliente->nome_empresa}*.\n\n";
        $message .= "Gostaríamos de informar que sua mensagem foi ouvida e acatada pela nossa gerência.\n";
        $message .= "Já tomamos as providências necessárias para corrigir o ponto que você nos sinalizou.\n";
        
        if ($avaliacao->resposta_dono) {
            $message .= "\nMensagem da gerência: \"{$avaliacao->resposta_dono}\"\n";
        }

        $message .= "\nObrigado por nos ajudar a melhorar!";
        return $message;
    }

    protected function buildNotificationMessage(Cliente $cliente, Avaliacao $avaliacao): string
    {
        $header = $cliente->pais === 'jp' ? "⚠️ 【重要】低評価通知" : "⚠️ *NOVA AVALIAÇÃO BAIXA* ⚠️";
        $noteTerm = $cliente->pais === 'jp' ? "評価" : "Nota";
        $feedbackTerm = $cliente->pais === 'jp' ? "フィードバック" : "Feedback";
        
        $message = "{$header}\n\n";
        $message .= "Shop: *{$cliente->nome_empresa}*\n";
        $message .= "{$noteTerm}: " . str_repeat("⭐", $avaliacao->nota) . " ({$avaliacao->nota}/5)\n\n";
        $message .= "{$feedbackTerm}: \"{$avaliacao->feedback}\"\n\n";
        $message .= "🔗 Dashboard: " . url("/cliente/{$cliente->id}");

        return $message;
    }

    public function sendWhatsApp(string $phone, string $message): bool
    {
        if (empty($this->waUrl)) return false;

        try {
            $response = Http::withHeaders([
                'apikey' => $this->waApiKey,
                'Content-Type' => 'application/json'
            ])->post("{$this->waUrl}/message/sendText/{$this->waInstance}", [
                'number' => $this->formatPhone($phone),
                'text' => $message
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('WhatsApp send error: ' . $e->getMessage());
            return false;
        }
    }

    public function sendLine(string $userId, string $message): bool
    {
        if (empty($this->lineToken)) return false;

        try {
            $response = Http::withToken($this->lineToken)
                ->post('https://api.line.me/v2/bot/message/push', [
                    'to' => $userId,
                    'messages' => [
                        [
                            'type' => 'text',
                            'text' => $message
                        ]
                    ]
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('LINE send error: ' . $e->getMessage());
            return false;
        }
    }

    protected function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) === 11) {
            $phone = '55' . $phone;
        }
        return $phone;
    }
}
