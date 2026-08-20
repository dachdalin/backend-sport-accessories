<?php

namespace App\Actions\Suppliers;

use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class UpdateSupplierAction
{
    /**
     * @param  array{name: string, contact_person: ?string, email: ?string, phone: ?string, address: ?string, status: bool}  $data
     */
    public function handle(Supplier $supplier, array $data): Supplier
    {
        return DB::transaction(function () use ($supplier, $data) {
            $supplier->update($data);

            return $supplier;
        });
    }
}
