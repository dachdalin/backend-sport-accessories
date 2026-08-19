<?php

namespace App\Actions\Colors;

use App\Models\Color;
use Illuminate\Support\Facades\DB;

class UpdateColorAction
{
    /**
     * @param  array{name: string, code: string}  $data
     */
    public function handle(Color $color, array $data): Color
    {
        DB::transaction(function () use ($color, $data) {
            $color->update($data);
        });

        return $color;
    }
}
