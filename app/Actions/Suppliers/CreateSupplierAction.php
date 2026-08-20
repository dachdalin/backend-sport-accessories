<?php

namespace App\Actions\Suppliers;

use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class CreateSupplierAction
{
    /**
     * @param  array{name: string, contact_person: ?string, email: ?string, phone: ?string, address: ?string, status: bool}  $data
     */
    public function handle(array $data): Supplier
    {
        return DB::transaction(fn () => Supplier::create($data));
    }
}
