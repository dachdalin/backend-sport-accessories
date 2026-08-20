<?php

namespace App\Actions\Warehouses;

use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class DeleteWarehouseAction
{
    public function handle(Warehouse $warehouse): void
    {
        DB::transaction(function () use ($warehouse) {
            $warehouse->delete();
        });
    }
}
