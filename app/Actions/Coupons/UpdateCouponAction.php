<?php

namespace App\Actions\Coupons;

use App\Models\Coupon;
use Illuminate\Support\Facades\DB;

class UpdateCouponAction
{
    /**
     * @param  array{code: string, type: string, value: float, min_order_amount: ?float, usage_limit: ?int, expires_at: ?string, status: bool}  $data
     */
    public function handle(Coupon $coupon, array $data): Coupon
    {
        DB::transaction(function () use ($coupon, $data) {
            $coupon->update($data);
        });

        return $coupon;
    }
}
