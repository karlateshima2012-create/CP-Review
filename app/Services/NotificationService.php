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
        $success = false;

        if ($cliente->canal_notificacao === 'whatsapp' && $cliente->telefone_whatsapp) {
            $success = $this->sendWhatsApp($cliente->telefone_whatsapp, $message, $cliente, $avaliacao);
        } elseif ($cliente->canal_notificacao === 'line' && $cliente->line_user_id) {
            $success = $this->sendLine($cliente->line_user_id, $message, $cliente, $avaliacao);
        }

        return $success;
    }

    /**
     * Fecha o loop com o cliente (notifica que o feedback foi resolvido)
     */
    public function closeLoop(Avaliacao $avaliacao): bool
    {
        $cliente = $avaliacao->tenant;
        $message = $this->buildCloseLoopMessage($cliente, $avaliacao);
        $success = false;

        if ($avaliacao->tipo_contato === 'whatsapp' && $avaliacao->contato_valor) {
            $success = $this->sendWhatsApp($avaliacao->contato_valor, $message, $cliente, $avaliacao);
        }

        return $success;
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

    public function sendWhatsApp(string $phone, string $message, Cliente $cliente = null, Avaliacao $avaliacao = null): bool
    {
        if (empty($this->waUrl)) {
            $this->logFail('whatsapp', $phone, $message, 'URL da API Evolution não configurada', $cliente, $avaliacao);
            return false;
        }

        try {
            $response = Http::withHeaders([
                'apikey' => $this->waApiKey,
                'Content-Type' => 'application/json'
            ])->post("{$this->waUrl}/message/sendText/{$this->waInstance}", [
                'number' => $this->formatPhone($phone),
                'text' => $message
            ]);

            $success = $response->successful();
            
            if ($success) {
                $this->logSuccess('whatsapp', $phone, $message, $cliente, $avaliacao);
            } else {
                $this->logFail('whatsapp', $phone, $message, "Erro API Evolution: " . $response->body(), $cliente, $avaliacao);
            }

            return $success;
        } catch (\Exception $e) {
            Log::error('WhatsApp send error: ' . $e->getMessage());
            $this->logFail('whatsapp', $phone, $message, $e->getMessage(), $cliente, $avaliacao);
            return false;
        }
    }

    public function sendLine(string $userId, string $message, Cliente $cliente = null, Avaliacao $avaliacao = null): bool
    {
        if (empty($this->lineToken)) {
            $this->logFail('line', $userId, $message, 'Token do LINE não configurado', $cliente, $avaliacao);
            return false;
        }

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

            $success = $response->successful();

            if ($success) {
                $this->logSuccess('line', $userId, $message, $cliente, $avaliacao);
            } else {
                $this->logFail('line', $userId, $message, "Erro API LINE: " . $response->body(), $cliente, $avaliacao);
            }

            return $success;
        } catch (\Exception $e) {
            Log::error('LINE send error: ' . $e->getMessage());
            $this->logFail('line', $userId, $message, $e->getMessage(), $cliente, $avaliacao);
            return false;
        }
    }

    protected function logSuccess($canal, $dest, $msg, $cliente, $avaliacao)
    {
        if (!$cliente) return;
        \App\Models\NotificacaoLog::create([
            'tenant_id' => $cliente->id,
            'avaliacao_id' => $avaliacao?->id,
            'canal' => $canal,
            'destinatario' => $dest,
            'mensagem' => $msg,
            'status' => 'enviada'
        ]);
    }

    protected function logFail($canal, $dest, $msg, $erro, $cliente, $avaliacao)
    {
        if (!$cliente) return;
        \App\Models\NotificacaoLog::create([
            'tenant_id' => $cliente->id,
            'avaliacao_id' => $avaliacao?->id,
            'canal' => $canal,
            'destinatario' => $dest,
            'mensagem' => $msg,
            'status' => 'falha',
            'erro_mensagem' => $erro
        ]);
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
