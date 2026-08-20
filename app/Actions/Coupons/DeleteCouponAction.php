<?php

namespace App\Actions\Coupons;

use App\Models\Coupon;
use Illuminate\Support\Facades\DB;

class DeleteCouponAction
{
    public function handle(Coupon $coupon): void
    {
        DB::transaction(function () use ($coupon) {
            $coupon->delete();
        });
    }
}
