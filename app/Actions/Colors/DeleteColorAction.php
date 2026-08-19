<?php

namespace App\Actions\Colors;

use App\Models\Color;
use Illuminate\Support\Facades\DB;

class DeleteColorAction
{
    public function handle(Color $color): void
    {
        DB::transaction(function () use ($color) {
            $color->delete();
        });
    }
}
