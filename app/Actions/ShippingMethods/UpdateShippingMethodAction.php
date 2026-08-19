<?php

namespace App\Actions\ShippingMethods;

use App\Models\ShippingMethod;
use Illuminate\Support\Facades\DB;

class UpdateShippingMethodAction
{
    /**
     * @param  array{title: string, cost: float, duration: ?string, status: bool}  $data
     */
    public function handle(ShippingMethod $shippingMethod, array $data): ShippingMethod
    {
        DB::transaction(function () use ($shippingMethod, $data) {
            $shippingMethod->update($data);
        });

        return $shippingMethod;
    }
}
