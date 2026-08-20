<?php

namespace App\Actions\Materials;

use App\Models\Material;
use Illuminate\Support\Facades\DB;

class DeleteMaterialAction
{
    public function handle(Material $material): void
    {
        DB::transaction(function () use ($material) {
            $material->delete();
        });
    }
}
