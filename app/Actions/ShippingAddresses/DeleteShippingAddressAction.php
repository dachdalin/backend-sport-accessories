<?php

namespace App\Actions\ShippingAddresses;

use App\Models\ShippingAddress;
use Illuminate\Support\Facades\DB;

class DeleteShippingAddressAction
{
    public function handle(ShippingAddress $shippingAddress): void
    {
        DB::transaction(function () use ($shippingAddress) {
            $shippingAddress->delete();
        });
    }
}
