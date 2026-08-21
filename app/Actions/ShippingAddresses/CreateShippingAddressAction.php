<?php

namespace App\Actions\ShippingAddresses;

use App\Models\ShippingAddress;
use Illuminate\Support\Facades\DB;

class CreateShippingAddressAction
{
    /**
     * @param  array{customer_id: int, contact_person_name: string, phone: ?string, address_type: string, address: string, city: string, state: ?string, zip: ?string, country: string, is_default: bool}  $data
     */
    public function handle(array $data): ShippingAddress
    {
        return DB::transaction(function () use ($data) {
            if ($data['is_default'] ?? false) {
                ShippingAddress::query()
                    ->where('customer_id', $data['customer_id'])
                    ->update(['is_default' => false]);
            }

            return ShippingAddress::create($data);
        });
    }
}
