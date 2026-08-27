<?php

namespace App\Actions\Customers;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class FindOrCreateSocialCustomerAction
{
    /**
     * Find the customer already linked to this social id, else link it to an existing
     * account matching the given email, else create a brand new customer.
     *
     * @param  'google_id'|'telegram_id'  $column
     * @param  array{name: string, email: string}  $attributes
     */
    public function handle(string $column, string $id, array $attributes): Customer
    {
        return DB::transaction(function () use ($column, $id, $attributes) {
            $customer = Customer::query()->where($column, $id)->first();

            if ($customer) {
                return $customer;
            }

            $customer = Customer::query()->where('email', $attributes['email'])->first();

            if ($customer) {
                $customer->update([$column => $id]);

                return $customer;
            }

            return Customer::create([...$attributes, $column => $id]);
        });
    }
}
