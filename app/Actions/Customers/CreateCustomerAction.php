<?php

namespace App\Actions\Customers;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class CreateCustomerAction
{
    /**
     * @param  array{name: string, email: string, phone: ?string, address: ?string, status: bool}  $data
     */
    public function handle(array $data): Customer
    {
        return DB::transaction(fn () => Customer::create($data));
    }
}
