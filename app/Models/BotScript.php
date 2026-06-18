<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class BotScript extends Model
{
    use HasUuids;

    protected $fillable = ['tenant_id', 'locale', 'messages'];

    protected $casts = [
        'messages' => 'array'
    ];

    public function tenant()
    {
        return $this->belongsTo(Cliente::class, 'tenant_id');
    }

    public static function getDefaultMessages($locale)
    {
        if ($locale === 'ja') {
            return [
                'welcome' => ['text' => "👋 ご来店ありがとうございました！", 'step' => 1],
                'q_first_visit' => ['text' => "当店は初めてですか？", 'step' => 2],
                'first_visit_ack' => ['text' => "ご来店ありがとうございます！", 'step' => 3],
                'askRate' => ['text' => "本日の体験はいかがでしたか？\n星をタップして評価をお願いします", 'step' => 4],
                'highRate' => ['text' => "💛 素晴らしい！スタッフ一同、大変喜んでおります。", 'step' => 5],
                'q_period' => ['text' => "どの時間帯でしたか？", 'step' => 6],
                'q_recommend' => ['text' => "お友達にもおすすめしたいですか？", 'step' => 7],
                'recommend_yes' => ['text' => "💛 そう言っていただけて光栄です！\nもしよろしければ、Googleでもこの体験を共有していただけませんか？", 'step' => 8],
                'recommend_maybe' => ['text' => "💛 そう言っていただけて光栄です！\nもしよろしければ、Googleでもこの体験を共有していただけませんか？", 'step' => 8],
                'recommend_no' => ['text' => "承知いたしました。良い体験をしていただけたようで何よりです。\n👉 Googleへの評価は、他のお客様の参考になりますので大変助かります。", 'step' => 8],
                'highFinalMsg' => ['text' => "🙏 貴重なお時間をいただき、本当にありがとうございます！\n🙌 またのご来店を心よりお待ちしております。", 'step' => 9],
                'lowRate' => ['text' => "💛 率直なご意見ありがとうございます。改善の参考にさせていただきます。", 'step' => 5],
                'lowRateQ' => ['text' => "ご不満に思われた点は何でしょうか？", 'step' => 6],
                'q_optional_text' => ['text' => "もう少し詳しく教えていただけますか？（任意）", 'step' => 7],
                'q_optional_photo' => ['text' => "よろしければ、写真も添付できます", 'step' => 8],
                'photo_ack' => ['text' => "💛 ありがとうございます！ 👍", 'step' => 9],
                'q_contact' => ['text' => "これについて、こちらの担当からご連絡させていただいてもよろしいでしょうか？", 'step' => 10],
                'lowFinalMsg' => ['text' => "🙏 ありがとうございます。いただいたご意見は直近の課題として関係者に共有いたしました。\n💛 お客様により良い体験をご提供できるよう、改善に努めてまいります。貴重なご意見をありがとうございました。", 'step' => 11],
            ];
        }

        return [
            'welcome' => ['text' => "👋 Obrigado pela visita!", 'step' => 1],
            'q_first_visit' => ['text' => "Primeira vez aqui?", 'step' => 2],
            'first_visit_ack' => ['text' => "Que bom saber disso!", 'step' => 3],
            'askRate' => ['text' => "Como foi sua experiência hoje?\nClique nas estrelas e nos dê uma nota", 'step' => 4],
            'highRate' => ['text' => "💛 Que incrível! Isso significa muito pra gente.", 'step' => 5],
            'q_period' => ['text' => "Veio em qual horário?", 'step' => 6],
            'q_recommend' => ['text' => "Você nos indicaria para um amigo?", 'step' => 7],
            'recommend_yes' => ['text' => "💛 Ficamos muito felizes em saber disso!\nSe puder, compartilhe essa experiência no Google também", 'step' => 8],
            'recommend_maybe' => ['text' => "💛 Ficamos muito felizes em saber disso!\nSe puder, compartilhe essa experiência no Google também", 'step' => 8],
            'recommend_no' => ['text' => "Tudo bem! Ficamos felizes que sua experiência foi boa.\n👉 Sua avaliação no Google faz toda diferença para novos clientes", 'step' => 8],
            'highFinalMsg' => ['text' => "🙏 Agradecemos de verdade pelo seu tempo!\n🙌 Muito obrigado! Até a próxima.", 'step' => 9],
            'lowRate' => ['text' => "💛 Obrigado pela sinceridade. Isso nos ajuda a melhorar.", 'step' => 5],
            'lowRateQ' => ['text' => "O que te deixou insatisfeito?", 'step' => 6],
            'q_optional_text' => ['text' => "Quer contar mais um pouquinho? (opcional)", 'step' => 7],
            'q_optional_photo' => ['text' => "Se quiser, pode enviar uma foto também", 'step' => 8],
            'photo_ack' => ['text' => "💛 Obrigado! 👍", 'step' => 9],
            'q_contact' => ['text' => "Podemos te responder sobre isso?", 'step' => 10],
            'lowFinalMsg' => ['text' => "🙏 Muito obrigado! Sua opinião já foi enviada para quem precisa resolver.\n💛 Nosso compromisso é melhorar. Agradecemos demais pela sinceridade.", 'step' => 11],
        ];
    }
}
