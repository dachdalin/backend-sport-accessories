<?php

namespace App\Actions\Sizes;

use App\Models\Size;
use Illuminate\Support\Facades\DB;

class DeleteSizeAction
{
    public function handle(Size $size): void
    {
        DB::transaction(function () use ($size) {
            $size->delete();
        });
    }
}
