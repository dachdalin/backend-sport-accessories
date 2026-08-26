<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\CouponType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ApplyCouponRequest;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CouponController extends Controller
{
    /**
     * Validate a coupon code against an order amount and return the discount it grants.
     */
    public function apply(ApplyCouponRequest $request): JsonResponse
    {
        $data = $request->validated();

        $coupon = Coupon::query()
            ->where('code', $data['code'])
            ->where('status', true)
            ->first();

        if (! $coupon) {
            throw ValidationException::withMessages([
                'code' => [__('This coupon code is invalid.')],
            ]);
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            throw ValidationException::withMessages([
                'code' => [__('This coupon code has expired.')],
            ]);
        }

        if ($coupon->min_order_amount && $data['order_amount'] < $coupon->min_order_amount) {
            throw ValidationException::withMessages([
                'order_amount' => [__('A minimum order amount of :amount is required for this coupon.', ['amount' => $coupon->min_order_amount])],
            ]);
        }

        $discountAmount = $coupon->type === CouponType::Percentage
            ? round($data['order_amount'] * ($coupon->value / 100), 2)
            : min((float) $coupon->value, $data['order_amount']);

        return response()->json([
            'data' => [
                'code' => $coupon->code,
                'type' => $coupon->type,
                'discount_amount' => $discountAmount,
                'payable_amount' => round($data['order_amount'] - $discountAmount, 2),
            ],
        ]);
    }
}
