<?php

namespace App\Actions\ShippingAddresses;

use App\Models\ShippingAddress;
use Illuminate\Support\Facades\DB;

class UpdateShippingAddressAction
{
    /**
     * @param  array{customer_id: int, contact_person_name: string, phone: ?string, address_type: string, address: string, city: string, state: ?string, zip: ?string, country: string, is_default: bool}  $data
     */
    public function handle(ShippingAddress $shippingAddress, array $data): ShippingAddress
    {
        DB::transaction(function () use ($shippingAddress, $data) {
            if ($data['is_default'] ?? false) {
                ShippingAddress::query()
                    ->where('customer_id', $data['customer_id'])
                    ->whereKeyNot($shippingAddress->id)
                    ->update(['is_default' => false]);
            }

            $shippingAddress->update($data);
        });

        return $shippingAddress;
    }
}
