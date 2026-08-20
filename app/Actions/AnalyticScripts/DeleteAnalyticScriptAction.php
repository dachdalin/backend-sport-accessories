<?php

namespace App\Actions\AnalyticScripts;

use App\Models\AnalyticScript;
use Illuminate\Support\Facades\DB;

class DeleteAnalyticScriptAction
{
    public function handle(AnalyticScript $analyticScript): void
    {
        DB::transaction(function () use ($analyticScript) {
            $analyticScript->delete();
        });
    }
}
