<?php

namespace App\Actions\SearchFunctions;

use App\Models\SearchFunction;
use Illuminate\Support\Facades\DB;

class DeleteSearchFunctionAction
{
    public function handle(SearchFunction $searchFunction): void
    {
        DB::transaction(function () use ($searchFunction) {
            $searchFunction->delete();
        });
    }
}
