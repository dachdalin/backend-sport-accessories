<?php

namespace App\Actions\Warehouses;

use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class CreateWarehouseAction
{
    /**
     * @param  array{name: string, code: string, address: ?string, city: ?string, country: ?string, phone: ?string, status: bool}  $data
     */
    public function handle(array $data): Warehouse
    {
        return DB::transaction(fn () => Warehouse::create($data));
    }
}
