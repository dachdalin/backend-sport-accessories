<?php

namespace App\Actions\Attributes;

use App\Models\Attribute;
use Illuminate\Support\Facades\DB;

class CreateAttributeAction
{
    /**
     * @param  array{name: string}  $data
     */
    public function handle(array $data): Attribute
    {
        return DB::transaction(fn () => Attribute::create($data));
    }
}
