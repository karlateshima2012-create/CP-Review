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
        $cliente = Cliente::where('slug', $slug)->firstOrFail();
        return view('avaliacao.index', compact('cliente'));
    }

    public function salvar(Request $request, $slug)
    {
        $cliente = Cliente::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'nota' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string',
            'problema' => 'nullable|string',
            'tipo_contato' => 'nullable|in:whatsapp,line,nao',
            'contato_valor' => 'nullable|string',
            'nome_cliente' => 'nullable|string|max:255',
            'primeira_visita' => 'nullable|boolean',
            'periodo_visita' => 'nullable|string',
            'foto_problema' => 'nullable|string' // base64
        ]);

        return DB::transaction(function() use ($validated, $cliente) {
            $token = Str::random(64);

            $avaliacao = Avaliacao::create([
                'tenant_id' => $cliente->id,
                'nota' => $validated['nota'],
                'feedback' => $validated['feedback'] ?? null,
                'problema' => $validated['problema'] ?? null,
                'nome_cliente' => $validated['nome_cliente'] ?? 'Anônimo',
                'tipo_contato' => $validated['tipo_contato'] ?? 'nao',
                'contato_valor' => $validated['contato_valor'] ?? null,
                'token_resposta' => $token,
                'primeira_visita' => $validated['primeira_visita'] ?? false,
                'periodo_visita' => $validated['periodo_visita'] ?? null
            ]);

            // Se for nota baixa, enviar notificação pelo canal configurado
            if ($avaliacao->nota <= 3) {
                $this->notificationService->notifyLowRating($cliente, $avaliacao);
            }

            return response()->json([
                'success' => true,
                'message' => 'Avaliação salva com sucesso',
                'token' => $token
            ]);
        });
    }

    public function botScript($slug)
    {
        $cliente = Cliente::where('slug', $slug)->firstOrFail();
        
        // Detect language from request or fallback to tenant country
        $locale = request('locale');
        if (!$locale || !in_array($locale, ['pt', 'ja'])) {
            $locale = $cliente->pais === 'jp' ? 'ja' : 'pt';
        }

        $translations = [
            'pt' => [
                'welcome' => "Olá! 👋 Bem-vindo ao suporte de qualidade da **{$cliente->nome_empresa}**.",
                'q_name' => "Para começarmos, como podemos te chamar?",
                'q_first_visit' => "Esta é sua primeira visita conosco?",
                'q_period' => "Em qual período foi sua visita?",
                'askRate' => "{name}, como foi sua experiência conosco hoje?",
                'lowRate' => "Poxa, lamento que não tenha sido perfeito. 😔",
                'lowRateQ' => "O que mais te incomodou hoje?",
                'highRate' => "Que notícia maravilhosa! 🤩",
                'highRateQ' => "O que você mais gostou na visita?",
                'detalhes' => "Quer deixar algum detalhe adicional ou sugestão?",
                'btn_yes' => "Sim, primeira vez! ✨",
                'btn_no' => "Já sou de casa! 🏠",
                'btn_lunch' => "Almoço ☀️",
                'btn_dinner' => "Jantar 🌙",
                'btn_other' => "Outro 🕒",
                'btnSend' => "Enviar Avaliação",
                'sending' => "Enviando...",
                'success' => "Recebido! 🚀",
                'finalMsg' => "Sua avaliação foi enviada diretamente para a nossa gerência.",
                'googleCTA' => "Como você teve uma ótima experiência, poderia nos ajudar avaliando no Google também?",
                'googleBtn' => "🚀 Abrir Google Reviews",
                'nextVisit' => "Trabalharemos duro para que sua próxima visita seja 5 estrelas!",
                'error' => "Ops! Algo deu errado.",
                'retryBtn' => "Tentar Novamente",
                'q_contact' => "Deixe seu e-mail ou WhatsApp (Opcional)",
                'optionsLow' => ['Atendimento 👤', 'Demora ⏰', 'Qualidade 🍽️', 'Limpeza 🧼', 'Preço 💰', 'Outro ⚙️'],
                'optionsHigh' => ['Sabor 😋', 'Atendimento 🤝', 'Velocidade ⚡', 'Ambiente ✨', 'Preço 💰']
            ],
            'ja' => [
                'welcome' => "こんにちは！ 👋 **{$cliente->nome_empresa}** の品質サポート窓口です。",
                'q_name' => "最初にお名前を教えていただけますか？",
                'q_first_visit' => "本日が初めてのご来店ですか？",
                'q_period' => "どの時間帯にご来店されましたか？",
                'askRate' => "{name}様、本日のご利用はいかがでしたか？",
                'lowRate' => "ご満足いただけず、大変申し訳ございません。 😔",
                'lowRateQ' => "どのような点が気になりましたか？",
                'highRate' => "嬉しいお言葉をありがとうございます！ 🤩",
                'highRateQ' => "一番良かった点はどこですか？",
                'detalhes' => "何か追加の詳細や提案はありますか？",
                'btn_yes' => "はい、初めてです！ ✨",
                'btn_no' => "以前も利用しました！ 🏠",
                'btn_lunch' => "ランチ ☀️",
                'btn_dinner' => "ディナー 🌙",
                'btn_other' => "その他 🕒",
                'btnSend' => "評価を送信",
                'sending' => "送信中...",
                'success' => "送信完了！ 🚀",
                'finalMsg' => "お客様の評価は直接マネージャーに届きました。",
                'googleCTA' => "素晴らしい体験をいただけたようで光栄です。Googleでのクチコミ投稿にもご協力いただけますか？",
                'googleBtn' => "🚀 Google レビューを開く",
                'nextVisit' => "次回は5つ星をいただけるよう、精一杯努めます！",
                'error' => "エラーが発生しました。",
                'retryBtn' => "もう一度試す",
                'q_contact' => "ご連絡先（メールまたはWhatsApp）を入力してください（任意）",
                'optionsLow' => ['接客 👤', '待ち時間 ⏰', '品質 🍽️', '清掃 🧼', '価格 💰', 'その他 ⚙️'],
                'optionsHigh' => ['味 😋', '接客 🤝', 'スピード ⚡', '雰囲気 ✨', '価格 💰']
            ]
        ];

        return response()->json([
            'tenant' => [
                'name' => $cliente->nome_empresa,
                'google_link' => $cliente->google_maps_link ?? "https://www.google.com/maps?q=" . urlencode($cliente->nome_empresa),
            ],
            'config' => [
                'auto_close' => 4000,
                'locale' => $locale
            ],
            'lang' => $translations[$locale] ?? $translations['pt']
        ]);
    }

    public function uploadMedia(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:10240', // 10MB
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
