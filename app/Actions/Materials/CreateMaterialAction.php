<?php

namespace App\Actions\Materials;

use App\Models\Material;
use Illuminate\Support\Facades\DB;

class CreateMaterialAction
{
    /**
     * @param  array{name: string, code: string}  $data
     */
    public function handle(array $data): Material
    {
        return DB::transaction(fn () => Material::create($data));
    }
}
