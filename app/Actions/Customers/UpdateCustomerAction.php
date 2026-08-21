<?php

namespace App\Actions\Customers;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class UpdateCustomerAction
{
    /**
     * @param  array{name: string, email: string, phone: ?string, address: ?string, status: bool}  $data
     */
    public function handle(Customer $customer, array $data): Customer
    {
        return DB::transaction(function () use ($customer, $data) {
            $customer->update($data);

            return $customer;
        });
    }
}
