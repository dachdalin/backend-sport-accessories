<?php

namespace App\Actions\SearchFunctions;

use App\Models\SearchFunction;
use Illuminate\Support\Facades\DB;

class UpdateSearchFunctionAction
{
    /**
     * @param  array{key: string, url: string, visible_for: string}  $data
     */
    public function handle(SearchFunction $searchFunction, array $data): SearchFunction
    {
        DB::transaction(function () use ($searchFunction, $data) {
            $searchFunction->update($data);
        });

        return $searchFunction;
    }
}
