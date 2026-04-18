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
                'welcome' => "👋 Obrigado pela visita!",
                'askRate' => "Como foi sua experiência hoje?",
                'highRate' => "💛 Que bom! Ficamos muito felizes com isso",
                'lowRate' => "💛 Obrigado pela sinceridade\nIsso nos ajuda a melhorar",
                'lowRateQ' => "O que aconteceu?",
                'q_first_visit' => "Primeira vez aqui?",
                'first_visit_ack' => "Legal 👍",
                'first_visit_ack_low' => "Obrigado 👍",
                'q_period' => "Veio em qual horário?",
                'period_ack' => "Entendi",
                'q_recommend' => "Indicaria pra um amigo?",
                'recommend_yes' => "🔥 Então você pode ajudar muita gente\nSe puder, deixa essa avaliação no Google também 🙌",
                'recommend_maybe' => "Já ajuda bastante se você deixar no Google 🙌",
                'recommend_no' => "Entendi 🙏 obrigado por responder",
                'q_optional_text' => "Quer contar mais um pouquinho? (opcional)",
                'q_optional_photo' => "Se quiser, pode enviar uma foto também",
                'q_contact' => "Quer que a gente te responda?",
                'contact_google' => "Se quiser, pode deixar sua avaliação no Google também",
                'googleBtn' => "⭐ Avaliar no Google",
                'highFinalMsg' => "🙏 Agradecemos de verdade\nAté a próxima 🙌",
                'lowFinalMsg' => "🙏 Obrigado por ajudar a gente a melhorar\nTenha um ótimo dia 🙌",
                'btn_yes' => "👍 Sim",
                'btn_no' => "🔄 Já conhecia",
                'btn_morning' => "🌅 Manhã",
                'btn_afternoon' => "🌤️ Tarde",
                'btn_night' => "🌙 Noite",
                'btn_rec_yes' => "😊 Sim",
                'btn_rec_maybe' => "🤔 Talvez",
                'btn_rec_no' => "🙅‍♂️ Não",
                'btn_contact_wa' => "📱 WhatsApp",
                'btn_contact_line' => "💬 LINE",
                'btn_contact_no' => "❌ Não preciso",
                'btn_skip' => "⏭️ Pular",
                'btn_send' => "📷 Enviar",
                'btn_send_txt' => "Enviar",
                'optionsLow' => ['😕 Atendimento', '⚙️ Serviço', '🧼 Ambiente', '💸 Preço', '⏱️ Demora', '❗ Outro']
            ],
            'ja' => [
                'welcome' => "👋 ご来店ありがとうございました！",
                'askRate' => "本日の体験はいかがでしたか？",
                'highRate' => "💛 よかったです！私たちもとても嬉しいです",
                'lowRate' => "💛 率直なご意見ありがとうございます\n改善の参考にさせていただきます",
                'lowRateQ' => "何がありましたか？",
                'q_first_visit' => "当店は初めてですか？",
                'first_visit_ack' => "なるほど 👍",
                'first_visit_ack_low' => "ありがとうございます 👍",
                'q_period' => "どの時間帯でしたか？",
                'period_ack' => "承知しました",
                'q_recommend' => "お友達にもおすすめしたいですか？",
                'recommend_yes' => "🔥 それなら多くの方の参考になります\nよろしければGoogleにもご感想をお願いします 🙌",
                'recommend_maybe' => "Googleに評価を残していただけるだけでも大変助かります 🙌",
                'recommend_no' => "わかりました 🙏 ご回答ありがとうございます",
                'q_optional_text' => "もう少し詳しく教えていただけますか？（任意）",
                'q_optional_photo' => "よろしければ、写真も添付できます",
                'q_contact' => "店舗からの返信をご希望ですか？",
                'contact_google' => "よろしければ、Googleにも評価を残していただけますか",
                'googleBtn' => "⭐ Googleで評価する",
                'highFinalMsg' => "🙏 心より感謝申し上げます\nまたのお越しをお待ちしております 🙌",
                'lowFinalMsg' => "🙏 改善へのご協力ありがとうございます\n良い一日を 🙌",
                'btn_yes' => "👍 はい",
                'btn_no' => "🔄 以前にも来た",
                'btn_morning' => "🌅 朝",
                'btn_afternoon' => "🌤️ 昼",
                'btn_night' => "🌙 夜",
                'btn_rec_yes' => "😊 はい",
                'btn_rec_maybe' => "🤔 たぶん",
                'btn_rec_no' => "🙅‍♂️ いいえ",
                'btn_contact_wa' => "📱 WhatsApp",
                'btn_contact_line' => "💬 LINE",
                'btn_contact_no' => "❌ 必要ない",
                'btn_skip' => "⏭️ スキップ",
                'btn_send' => "📷 送信する",
                'btn_send_txt' => "送信",
                'optionsLow' => ['😕 接客', '⚙️ サービス', '🧼 環境', '💸 価格', '⏱️ 待ち時間', '❗ その他']
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
