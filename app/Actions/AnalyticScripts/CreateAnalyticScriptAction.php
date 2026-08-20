<?php

namespace App\Actions\AnalyticScripts;

use App\Models\AnalyticScript;
use Illuminate\Support\Facades\DB;

class CreateAnalyticScriptAction
{
    /**
     * @param  array{name: string, type: string, script_id: ?string, script: string, status: bool}  $data
     */
    public function handle(array $data): AnalyticScript
    {
        return DB::transaction(fn () => AnalyticScript::create($data));
    }
}
