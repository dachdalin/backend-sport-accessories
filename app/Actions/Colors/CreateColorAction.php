<?php

namespace App\Actions\Colors;

use App\Models\Color;
use Illuminate\Support\Facades\DB;

class CreateColorAction
{
    /**
     * @param  array{name: string, code: string}  $data
     */
    public function handle(array $data): Color
    {
        return DB::transaction(fn () => Color::create($data));
    }
}
