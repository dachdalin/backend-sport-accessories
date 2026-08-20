<?php

namespace App\Actions\Warehouses;

use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class UpdateWarehouseAction
{
    /**
     * @param  array{name: string, code: string, address: ?string, city: ?string, country: ?string, phone: ?string, status: bool}  $data
     */
    public function handle(Warehouse $warehouse, array $data): Warehouse
    {
        return DB::transaction(function () use ($warehouse, $data) {
            $warehouse->update($data);

            return $warehouse;
        });
    }
}
