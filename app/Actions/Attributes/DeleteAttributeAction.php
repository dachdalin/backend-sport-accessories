<?php

namespace App\Actions\Attributes;

use App\Models\Attribute;
use Illuminate\Support\Facades\DB;

class DeleteAttributeAction
{
    public function handle(Attribute $attribute): void
    {
        DB::transaction(function () use ($attribute) {
            $attribute->delete();
        });
    }
}
