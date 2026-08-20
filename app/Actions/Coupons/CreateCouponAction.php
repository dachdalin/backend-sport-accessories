<?php

namespace App\Actions\Coupons;

use App\Models\Coupon;
use Illuminate\Support\Facades\DB;

class CreateCouponAction
{
    /**
     * @param  array{code: string, type: string, value: float, min_order_amount: ?float, usage_limit: ?int, expires_at: ?string, status: bool}  $data
     */
    public function handle(array $data): Coupon
    {
        return DB::transaction(fn () => Coupon::create($data));
    }
}
