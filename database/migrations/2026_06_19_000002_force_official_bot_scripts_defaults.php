<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\BotScript;

return new class extends Migration
{
    public function up()
    {
        // Overwrite all existing bot scripts with the official flow defaults
        $scripts = DB::table('bot_scripts')->get();

        foreach ($scripts as $script) {
            $locale = $script->locale;
            $defaults = BotScript::getDefaultMessages($locale);

            DB::table('bot_scripts')
                ->where('id', $script->id)
                ->update(['messages' => json_encode($defaults)]);
        }
    }

    public function down()
    {
        // No rollback needed
    }
};
