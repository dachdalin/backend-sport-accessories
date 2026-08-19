<?php

namespace App\Actions\ShippingMethods;

use App\Models\ShippingMethod;
use Illuminate\Support\Facades\DB;

class CreateShippingMethodAction
{
    /**
     * @param  array{title: string, cost: float, duration: ?string, status: bool, creator_id: int, creator_type: string}  $data
     */
    public function handle(array $data): ShippingMethod
    {
        return DB::transaction(fn () => ShippingMethod::create($data));
    }
}
