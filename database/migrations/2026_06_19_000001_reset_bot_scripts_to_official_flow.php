<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\BotScript;

return new class extends Migration
{
    public function up()
    {
        // Get all bot scripts
        $scripts = DB::table('bot_scripts')->get();

        foreach ($scripts as $script) {
            $locale = $script->locale;
            $messages = json_decode($script->messages, true);

            if (!is_array($messages)) {
                $messages = [];
            }

            $defaults = BotScript::getDefaultMessages($locale);
            $newMessages = [];

            foreach ($defaults as $key => $defaultVal) {
                $existingVal = $messages[$key] ?? null;
                $text = '';
                if (is_array($existingVal)) {
                    $text = $existingVal['text'] ?? $defaultVal['text'];
                } elseif (is_string($existingVal)) {
                    $text = $existingVal;
                } else {
                    $text = $defaultVal['text'];
                }

                // Force step value from the new official flow defaults
                $newMessages[$key] = [
                    'text' => $text,
                    'step' => $defaultVal['step'],
                ];
            }

            DB::table('bot_scripts')
                ->where('id', $script->id)
                ->update(['messages' => json_encode($newMessages)]);
        }
    }

    public function down()
    {
        // No rollback needed
    }
};
