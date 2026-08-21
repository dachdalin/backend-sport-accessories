<?php

namespace App\Actions\Attributes;

use App\Models\Attribute;
use Illuminate\Support\Facades\DB;

class UpdateAttributeAction
{
    /**
     * @param  array{name: string}  $data
     */
    public function handle(Attribute $attribute, array $data): Attribute
    {
        DB::transaction(function () use ($attribute, $data) {
            $attribute->update($data);
        });

        return $attribute;
    }
}
