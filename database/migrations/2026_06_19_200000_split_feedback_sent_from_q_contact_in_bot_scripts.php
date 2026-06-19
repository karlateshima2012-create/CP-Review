<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $splits = [
        'pt' => [
            'feedback_sent' => "Entendemos sua insatisfação e seu feedback foi encaminhado ao responsável para análise e melhoria dos nossos serviços.",
            'q_contact'     => "Deseja que a empresa entre em contato para entender melhor o ocorrido e buscar uma solução?",
            'step_feedback' => 5,
            'step_contact'  => 6,
            'step_final'    => 7,
        ],
        'ja' => [
            'feedback_sent' => "いただいたご意見は改善のため担当者に共有いたしました。",
            'q_contact'     => "より詳しいお話を伺い解決策をご提案するため、担当者よりご連絡させていただいてもよろしいでしょうか？",
            'step_feedback' => 5,
            'step_contact'  => 6,
            'step_final'    => 7,
        ],
        'en' => [
            'feedback_sent' => "We truly appreciate your feedback and have shared it with our team.",
            'q_contact'     => "Would you like us to reach out to discuss this further and find a solution?",
            'step_feedback' => 5,
            'step_contact'  => 6,
            'step_final'    => 7,
        ],
    ];

    public function up(): void
    {
        $scripts = DB::table('bot_scripts')->get();

        foreach ($scripts as $script) {
            $messages = json_decode($script->messages, true);
            if (!is_array($messages)) continue;

            $locale = $script->locale;
            if (!isset($this->splits[$locale])) continue;

            $split = $this->splits[$locale];
            $changed = false;

            // Add feedback_sent if not present
            if (!isset($messages['feedback_sent'])) {
                $messages['feedback_sent'] = [
                    'text' => $split['feedback_sent'],
                    'step' => $split['step_feedback'],
                ];
                $changed = true;
            }

            // Update q_contact to just the question (strip the old combined text)
            if (isset($messages['q_contact'])) {
                $messages['q_contact']['text'] = $split['q_contact'];
                $messages['q_contact']['step'] = $split['step_contact'];
                $changed = true;
            }

            // Update lowFinalMsg step to 7
            if (isset($messages['lowFinalMsg'])) {
                $messages['lowFinalMsg']['step'] = $split['step_final'];
                $changed = true;
            }

            if ($changed) {
                DB::table('bot_scripts')
                    ->where('id', $script->id)
                    ->update(['messages' => json_encode($messages, JSON_UNESCAPED_UNICODE)]);
            }
        }
    }

    public function down(): void
    {
        $originals = [
            'pt' => "Entendemos sua insatisfação e seu feedback foi encaminhado ao responsável para análise e melhoria dos nossos serviços. Deseja que a empresa entre em contato para entender melhor o ocorrido e buscar uma solução?",
            'ja' => "いただいたご意見は改善のため担当者に共有いたしました。より詳しいお話を伺い解決策をご提案するため、担当者よりご連絡させていただいてもよろしいでしょうか？",
            'en' => "We truly appreciate your feedback and have shared it with our team. Would you like us to reach out to discuss this further and find a solution?",
        ];

        $scripts = DB::table('bot_scripts')->get();
        foreach ($scripts as $script) {
            $messages = json_decode($script->messages, true);
            if (!is_array($messages) || !isset($originals[$script->locale])) continue;

            unset($messages['feedback_sent']);
            if (isset($messages['q_contact'])) {
                $messages['q_contact']['text'] = $originals[$script->locale];
                $messages['q_contact']['step'] = 5;
            }
            if (isset($messages['lowFinalMsg'])) {
                $messages['lowFinalMsg']['step'] = 6;
            }

            DB::table('bot_scripts')
                ->where('id', $script->id)
                ->update(['messages' => json_encode($messages, JSON_UNESCAPED_UNICODE)]);
        }
    }
};
