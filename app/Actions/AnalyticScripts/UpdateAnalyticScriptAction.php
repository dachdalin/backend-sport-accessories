<?php

namespace App\Actions\AnalyticScripts;

use App\Models\AnalyticScript;
use Illuminate\Support\Facades\DB;

class UpdateAnalyticScriptAction
{
    /**
     * @param  array{name: string, type: string, script_id: ?string, script: string, status: bool}  $data
     */
    public function handle(AnalyticScript $analyticScript, array $data): AnalyticScript
    {
        return DB::transaction(function () use ($analyticScript, $data) {
            $analyticScript->update($data);

            return $analyticScript;
        });
    }
}
