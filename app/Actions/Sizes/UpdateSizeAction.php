<?php

namespace App\Actions\Sizes;

use App\Models\Size;
use Illuminate\Support\Facades\DB;

class UpdateSizeAction
{
    /**
     * @param  array{name: string, code: string}  $data
     */
    public function handle(Size $size, array $data): Size
    {
        DB::transaction(function () use ($size, $data) {
            $size->update($data);
        });

        return $size;
    }
}
