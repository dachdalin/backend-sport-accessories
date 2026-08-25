<?php

namespace App\Actions\Customers;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class RegisterCustomerAction
{
    /**
     * @param  array{name: string, email: string, password: string, phone: ?string, address: ?string}  $data
     */
    public function handle(array $data): Customer
    {
        return DB::transaction(fn () => Customer::create($data));
    }
}
