<?php

namespace App\Actions\Sizes;

use App\Models\Size;
use Illuminate\Support\Facades\DB;

class CreateSizeAction
{
    /**
     * @param  array{name: string, code: string}  $data
     */
    public function handle(array $data): Size
    {
        return DB::transaction(fn () => Size::create($data));
    }
}
