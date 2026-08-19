<?php

namespace App\Actions\SearchFunctions;

use App\Models\SearchFunction;
use Illuminate\Support\Facades\DB;

class CreateSearchFunctionAction
{
    /**
     * @param  array{key: string, url: string, visible_for: string}  $data
     */
    public function handle(array $data): SearchFunction
    {
        return DB::transaction(fn () => SearchFunction::create($data));
    }
}
