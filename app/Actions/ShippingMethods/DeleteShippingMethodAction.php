<?php

namespace App\Actions\ShippingMethods;

use App\Models\ShippingMethod;
use Illuminate\Support\Facades\DB;

class DeleteShippingMethodAction
{
    public function handle(ShippingMethod $shippingMethod): void
    {
        DB::transaction(function () use ($shippingMethod) {
            $shippingMethod->delete();
        });
    }
}
