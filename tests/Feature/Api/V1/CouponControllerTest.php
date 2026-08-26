<?php

namespace Tests\Feature\Api\V1;

use App\Enums\CouponType;
use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_fixed_coupon_is_applied(): void
    {
        $coupon = Coupon::factory()->create([
            'code' => 'SAVE10',
            'type' => CouponType::Fixed,
            'value' => 10,
            'min_order_amount' => 50,
        ]);

        $response = $this->postJson(route('api.v1.coupons.apply'), [
            'code' => 'save10',
            'order_amount' => 100,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.code', $coupon->code)
            ->assertJsonPath('data.discount_amount', 10)
            ->assertJsonPath('data.payable_amount', 90);
    }

    public function test_percentage_coupon_is_applied(): void
    {
        Coupon::factory()->create([
            'code' => 'SAVE20',
            'type' => CouponType::Percentage,
            'value' => 20,
            'min_order_amount' => null,
        ]);

        $response = $this->postJson(route('api.v1.coupons.apply'), [
            'code' => 'SAVE20',
            'order_amount' => 100,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.discount_amount', 20)
            ->assertJsonPath('data.payable_amount', 80);
    }

    public function test_unknown_code_is_rejected(): void
    {
        $response = $this->postJson(route('api.v1.coupons.apply'), [
            'code' => 'MISSING',
            'order_amount' => 100,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('code');
    }

    public function test_inactive_coupon_is_rejected(): void
    {
        $coupon = Coupon::factory()->create(['status' => false]);

        $response = $this->postJson(route('api.v1.coupons.apply'), [
            'code' => $coupon->code,
            'order_amount' => 100,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('code');
    }

    public function test_expired_coupon_is_rejected(): void
    {
        $coupon = Coupon::factory()->create(['expires_at' => now()->subDay()]);

        $response = $this->postJson(route('api.v1.coupons.apply'), [
            'code' => $coupon->code,
            'order_amount' => 100,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('code');
    }

    public function test_order_below_minimum_amount_is_rejected(): void
    {
        $coupon = Coupon::factory()->create(['min_order_amount' => 100]);

        $response = $this->postJson(route('api.v1.coupons.apply'), [
            'code' => $coupon->code,
            'order_amount' => 50,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('order_amount');
    }
}
