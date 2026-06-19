<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Avaliacao;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AvaliacaoController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function show($slug)
    {
        $cliente = Cliente::where('slug', $slug)->first();

        if (!$cliente) abort(404);

        // Registra cada acesso ao bot para métricas de conversão
        \DB::table('bot_acessos')->insert([
            'tenant_id'  => $cliente->id,
            'created_at' => now(),
        ]);

        return view('avaliacao.index', compact('cliente'));
    }

    public function salvar(Request $request, $slug)
    {
        $cliente = Cliente::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'nota' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string',
            'problema' => 'nullable|string',
            'tipo_contato' => 'nullable|in:whatsapp,line,email,nao',
            'contato_valor' => 'nullable|string',
            'nome_cliente' => 'nullable|string|max:255',
            'primeira_visita' => 'nullable|boolean',
            'periodo_visita' => 'nullable|string',
            'foto_problema' => 'nullable|string' // base64
        ]);

        return DB::transaction(function() use ($validated, $cliente) {
            $token = Str::random(64);

            $feedback = isset($validated['feedback']) ? htmlspecialchars(strip_tags($validated['feedback']), ENT_QUOTES, 'UTF-8') : null;
            $problema = isset($validated['problema']) ? htmlspecialchars(strip_tags($validated['problema']), ENT_QUOTES, 'UTF-8') : null;
            $nome_cliente = isset($validated['nome_cliente']) ? htmlspecialchars(strip_tags($validated['nome_cliente']), ENT_QUOTES, 'UTF-8') : 'Anônimo';
            $contato_valor = isset($validated['contato_valor']) ? htmlspecialchars(strip_tags($validated['contato_valor']), ENT_QUOTES, 'UTF-8') : null;

            $avaliacao = Avaliacao::create([
                'tenant_id' => $cliente->id,
                'nota' => $validated['nota'],
                'feedback' => $feedback,
                'problema' => $problema,
                'nome_cliente' => $nome_cliente,
                'tipo_contato' => $validated['tipo_contato'] ?? 'nao',
                'contato_valor' => $contato_valor,
                'token_resposta' => $token,
                'primeira_visita' => $validated['primeira_visita'] ?? false,
                'periodo_visita' => $validated['periodo_visita'] ?? null
            ]);

            // Se for nota baixa, enviar notificação pelo canal configurado em segundo plano
            if ($avaliacao->nota <= 3) {
                \App\Jobs\SendLowRatingNotification::dispatch($cliente, $avaliacao);
            }

            return response()->json([
                'success' => true,
                'message' => 'Avaliação salva com sucesso',
                'token' => $token
            ]);
        });
    }

    protected function getDefaultMessages($locale)
    {
        return \App\Models\BotScript::getDefaultMessages($locale);
    }

    protected function getStaticTranslations($locale, Cliente $cliente = null)
    {
        $selectedKeys = ($cliente && \Illuminate\Support\Facades\Schema::hasColumn('clientes', 'motivos_problema') && $cliente->motivos_problema) 
            ? $cliente->motivos_problema 
            : ['atendimento', 'produto_servico', 'preco', 'demora', 'outro'];

        $mapping = [
            'pt' => [
                'atendimento' => '😕 Atendimento',
                'produto_servico' => '⚙️ Produto ou Serviço',
                'preco' => '💸 Preço',
                'demora' => '⏱️ Demora',
                'limpeza' => '🧹 Limpeza',
                'conforto' => '🪑 Conforto',
                'entrega' => '📦 Entrega',
                'outro' => '❗ Outro'
            ],
            'ja' => [
                'atendimento' => '😕 接客',
                'produto_servico' => '⚙️ 商品またはサービス',
                'preco' => '💸 価格',
                'demora' => '⏱️ 待ち時間',
                'limpeza' => '🧹 清潔さ',
                'conforto' => '🪑 快適さ',
                'entrega' => '📦 配達',
                'outro' => '❗ その他'
            ],
            'en' => [
                'atendimento' => '😕 Service',
                'produto_servico' => '⚙️ Product or Service',
                'preco' => '💸 Price',
                'demora' => '⏱️ Wait time',
                'limpeza' => '🧹 Cleanliness',
                'conforto' => '🪑 Comfort',
                'entrega' => '📦 Delivery',
                'outro' => '❗ Other'
            ]
        ];

        $optionsLow = [];
        foreach ($selectedKeys as $key) {
            if (isset($mapping[$locale][$key])) {
                $optionsLow[] = $mapping[$locale][$key];
            }
        }

        if ($locale === 'en') {
            return [
                'btn_yes' => "👍 Yes",
                'btn_no' => "🔄 Been here before",
                'btn_morning' => "🌅 Morning",
                'btn_afternoon' => "🌤️ Afternoon",
                'btn_night' => "🌙 Evening",
                'btn_rec_yes' => "✨ Of course!",
                'btn_rec_maybe' => "👍 Yes",
                'btn_rec_no' => "😶 No",
                'btn_contact_yes' => "📱 Yes",
                'btn_contact_no' => "❌ No",
                'btn_choose_line' => "💬 LINE",
                'btn_choose_email' => "📧 E-mail",
                'btn_contact_line' => "💬 LINE",
                'btn_contact_email' => "📧 E-mail",
                'btn_skip' => "⏭️ Skip",
                'btn_send' => "📸 Send",
                'btn_send_txt' => "Send",
                'btn_feedback_no' => "Skip",
                'btn_feedback_send' => "Send",
                'feedback_placeholder' => "✍️ Type your message...",
                'googleBtn' => "⭐ Review on Google",
                'optionsLow' => $optionsLow
            ];
        }

        if ($locale === 'ja') {
            return [
                'btn_yes' => "👍 はい",
                'btn_no' => "🔄 以前にも来た",
                'btn_morning' => "🌅 朝",
                'btn_afternoon' => "🌤️ 昼",
                'btn_night' => "🌙 夜",
                'btn_rec_yes' => "✨ もちろんです！",
                'btn_rec_maybe' => "👍 はい",
                'btn_rec_no' => "😶 いいえ",
                'btn_contact_yes' => "📱 はい",
                'btn_contact_no' => "❌ いいえ",
                'btn_choose_line' => "💬 LINE",
                'btn_choose_email' => "📧 メールアドレス",
                'btn_contact_line' => "💬 LINE",
                'btn_contact_email' => "📧 E-mail",
                'btn_contact_no' => "❌ 必要ない",
                'btn_skip' => "⏭️ スキップ",
                'btn_send' => "📸 送信する",
                'btn_send_txt' => "送信",
                'btn_feedback_no' => "スキップ",
                'btn_feedback_send' => "送信",
                'feedback_placeholder' => "✍️ メッセージを入力してください...",
                'googleBtn' => "⭐ Googleで評価する",
                'optionsLow' => $optionsLow
            ];
        }

        return [
            'btn_yes' => "👍 Sim",
            'btn_no' => "🔄 Já conhecia",
            'btn_morning' => "🌅 Manhã",
            'btn_afternoon' => "🌤️ Tarde",
            'btn_night' => "🌙 Noite",
            'btn_rec_yes' => "✨ Com certeza!",
            'btn_rec_maybe' => "👍 Sim",
            'btn_rec_no' => "😶 Não",
            'btn_contact_yes' => "📱 Sim",
            'btn_contact_no' => "❌ Não",
            'btn_choose_wa' => "📱 Informar WhatsApp",
            'btn_choose_email' => "📧 Informar E-mail",
            'btn_contact_wa' => "📱 WhatsApp",
            'btn_contact_email' => "📧 E-mail",
            'btn_contact_no' => "❌ Não precisa",
            'btn_skip' => "⏭️ Pular",
            'btn_send' => "📸 Enviar",
            'btn_send_txt' => "Enviar",
            'btn_feedback_no' => "Pular",
            'btn_feedback_send' => "Enviar",
            'feedback_placeholder' => "✍️ Digite sua mensagem...",
            'googleBtn' => "⭐ Avaliar no Google",
            'optionsLow' => $optionsLow
        ];
    }

    public function botScript($slug)
    {
        $cliente = Cliente::where('slug', $slug)->firstOrFail();
        
        // Detect language from request or fallback to tenant country
        $locale = request('locale');
        if (!$locale || !in_array($locale, ['pt', 'ja', 'en'])) {
            $locale = $cliente->pais === 'jp' ? 'ja' : 'pt';
        }

        $botScript = \App\Models\BotScript::where('tenant_id', $cliente->id)
            ->where('locale', $locale)
            ->first();

        $defaults = $this->getDefaultMessages($locale);
        $savedMessages = $botScript ? $botScript->messages : [];

        $messages = [];
        foreach ($defaults as $key => $defaultVal) {
            $savedText = $savedMessages[$key]['text'] ?? null;
            $savedStep = $savedMessages[$key]['step'] ?? null;

            $step = ($savedStep !== null && $savedStep !== '')
                ? (int) $savedStep
                : $defaultVal['step'];

            $messages[$key] = [
                'text' => ($savedText !== null && $savedText !== '') ? $savedText : $defaultVal['text'],
                'step' => $step,
            ];
        }

        $static = $this->getStaticTranslations($locale, $cliente);
        $lang = array_merge($static, $messages);

        return response()->json([
            'tenant' => [
                'name' => $cliente->nome_empresa,
                'google_link' => $cliente->google_maps_link ?? "https://www.google.com/maps?q=" . urlencode($cliente->nome_empresa),
                'logo_url' => $cliente->logo_url,
                'cover_url' => $cliente->cover_url,
            ],
            'config' => [
                'auto_close' => 4000,
                'locale' => $locale
            ],
            'lang' => $lang
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function uploadMedia(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,jpg,png,webp|max:10240', // 10MB
            'review_token' => 'required|string',
        ]);

        $avaliacao = Avaliacao::where('token_resposta', $request->review_token)->firstOrFail();
        
        $path = $request->file('photo')->store('avaliacoes', 'public');
        
        $avaliacao->update([
            'foto_problema' => $path
        ]);

        return response()->json(['success' => true, 'path' => $path]);
    }
}
