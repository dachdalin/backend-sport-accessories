<?php

namespace App\Actions\Materials;

use App\Models\Material;
use Illuminate\Support\Facades\DB;

class UpdateMaterialAction
{
    /**
     * @param  array{name: string, code: string}  $data
     */
    public function handle(Material $material, array $data): Material
    {
        DB::transaction(function () use ($material, $data) {
            $material->update($data);
        });

        return $material;
    }
}
