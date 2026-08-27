<?php

namespace App\Actions\Customers;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class DeleteCustomerAction
{
    public function handle(Customer $customer): void
    {
        DB::transaction(function () use ($customer) {
            $customer->tokens()->delete();
            $customer->delete();
        });
    }
}
