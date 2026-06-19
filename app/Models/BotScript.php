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
        if ($locale === 'en') {
            return [
                'welcome'         => ['text' => "How was your experience today?", 'step' => 1],
                'q_first_visit'   => ['text' => "Is this your first time here?", 'step' => null],
                'first_visit_ack' => ['text' => "Welcome! We're so glad to have you!", 'step' => null],
                'askRate'         => ['text' => "", 'step' => null],
                'highRate'        => ['text' => "Thank you so much! We're delighted to hear that! 😊", 'step' => 2],
                'q_period'        => ['text' => "What time did you visit us?", 'step' => null],
                'q_recommend'     => ['text' => "Would you mind sharing a quick review on Google?", 'step' => 3],
                'recommend_yes'   => ['text' => "", 'step' => null],
                'recommend_maybe' => ['text' => "", 'step' => null],
                'recommend_no'    => ['text' => "", 'step' => null],
                'highFinalMsg'    => ['text' => "Thank you for your support and for taking a moment to share your experience. ❤️\n\nHave a wonderful day!", 'step' => 4],
                'lowRate'         => ['text' => "😔 We're truly sorry your experience wasn't ideal.", 'step' => 2],
                'lowRateQ'        => ['text' => "What could we have done better?", 'step' => 3],
                'q_optional_text' => ['text' => "Would you like to share more details?", 'step' => 4],
                'q_optional_photo'=> ['text' => "Feel free to attach a photo if you'd like.", 'step' => null],
                'photo_ack'       => ['text' => "Thank you! 👍", 'step' => null],
                'q_contact'       => ['text' => "We truly appreciate your feedback and have shared it with our team. Would you like us to reach out to discuss this further and find a solution?", 'step' => 5],
                'lowFinalMsg'     => ['text' => "Thank you for sharing your experience with us.", 'step' => 6],
            ];
        }

        if ($locale === 'ja') {
            return [
                'welcome' => ['text' => "本日の体験はいかがでしたでしょうか？", 'step' => 1],
                'q_first_visit' => ['text' => "当店は初めてですか？", 'step' => null],
                'first_visit_ack' => ['text' => "ご来店ありがとうございます！", 'step' => null],
                'askRate' => ['text' => "", 'step' => null],
                'highRate' => ['text' => "高評価をいただき、誠にありがとうございます。励みになります！", 'step' => 2],
                'q_period' => ['text' => "どの時間帯でしたか？", 'step' => null],
                'q_recommend' => ['text' => "Googleでレビューを書いていただけませんか？", 'step' => 3],
                'recommend_yes' => ['text' => "", 'step' => null],
                'recommend_maybe' => ['text' => "", 'step' => null],
                'recommend_no' => ['text' => "", 'step' => null],
                'highFinalMsg' => ['text' => "貴重なお時間をいただき、本当にありがとうございます。❤️\n\n素晴らしい一日をお過ごしください！", 'step' => 4],
                'lowRate' => ['text' => "😔 ご期待に沿えず、大変申し訳ございませんでした。", 'step' => 2],
                'lowRateQ' => ['text' => "ご満足いただけなかった点について教えていただけますか？", 'step' => 3],
                'q_optional_text' => ['text' => "詳細を教えていただけますでしょうか？", 'step' => 4],
                'q_optional_photo' => ['text' => "よろしければ、写真も添付できます", 'step' => null],
                'photo_ack' => ['text' => "ありがとうございます！ 👍", 'step' => null],
                'q_contact' => ['text' => "いただいたご意見は改善のため担当者に共有いたしました。より詳しいお話を伺い解決策をご提案するため、担当者よりご連絡させていただいてもよろしいでしょうか？", 'step' => 5],
                'lowFinalMsg' => ['text' => "ご意見を共有していただき、誠にありがとうございました。", 'step' => 6],
            ];
        }

        return [
            'welcome' => ['text' => "Como foi sua experiência hoje?", 'step' => 1],
            'q_first_visit' => ['text' => "Primeira vez aqui?", 'step' => null],
            'first_visit_ack' => ['text' => "Que bom saber disso!", 'step' => null],
            'askRate' => ['text' => "", 'step' => null],
            'highRate' => ['text' => "Que bom saber disso! 😊", 'step' => 2],
            'q_period' => ['text' => "Veio em qual horário?", 'step' => null],
            'q_recommend' => ['text' => "Você pode deixar uma avaliação rápida no Google?", 'step' => 3],
            'recommend_yes' => ['text' => "", 'step' => null],
            'recommend_maybe' => ['text' => "", 'step' => null],
            'recommend_no' => ['text' => "", 'step' => null],
            'highFinalMsg' => ['text' => "Obrigado pelo seu apoio e por dedicar alguns segundos para nos avaliar. ❤️\n\nTenha um excelente dia!", 'step' => 4],
            'lowRate' => ['text' => "😔 Sentimos muito que sua experiência não tenha sido a ideal.", 'step' => 2],
            'lowRateQ' => ['text' => "O que mais impactou sua experiência?", 'step' => 3],
            'q_optional_text' => ['text' => "Gostaria de nos contar mais detalhes?", 'step' => 4],
            'q_optional_photo' => ['text' => "Se quiser, pode enviar uma foto também", 'step' => null],
            'photo_ack' => ['text' => "Obrigado! 👍", 'step' => null],
            'q_contact' => ['text' => "Entendemos sua insatisfação e seu feedback foi encaminhado ao responsável para análise e melhoria dos nossos serviços. Deseja que a empresa entre em contato para entender melhor o ocorrido e buscar uma solução?", 'step' => 5],
            'lowFinalMsg' => ['text' => "Obrigado por compartilhar sua experiência conosco.", 'step' => 6],
        ];
    }
}
