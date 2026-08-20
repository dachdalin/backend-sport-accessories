<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_coupons_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Coupon::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('coupons.index'));

        $response->assertOk();
    }

    public function test_coupon_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('coupons.create'));

        $response->assertOk();
    }

    public function test_coupon_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('coupons.store'), [
                'code' => 'save10',
                'type' => 'fixed',
                'value' => '10.00',
                'min_order_amount' => '50.00',
                'usage_limit' => '100',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('coupons.index'));

        $coupon = Coupon::sole();

        $this->assertSame('SAVE10', $coupon->code);
        $this->assertTrue($coupon->status);
    }

    public function test_coupon_code_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('coupons.store'), [
                'code' => '',
            ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_coupon_percentage_value_cannot_exceed_100(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('coupons.store'), [
                'code' => 'BIGDISCOUNT',
                'type' => 'percentage',
                'value' => '150',
            ]);

        $response->assertSessionHasErrors('value');
    }

    public function test_coupon_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $coupon = Coupon::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('coupons.edit', $coupon));

        $response->assertOk();
    }

    public function test_coupon_can_be_updated(): void
    {
        $user = User::factory()->create();
        $coupon = Coupon::factory()->create(['status' => true]);

        $response = $this
            ->actingAs($user)
            ->put(route('coupons.update', $coupon), [
                'code' => 'newcode',
                'type' => 'fixed',
                'value' => '15.00',
                'status' => '0',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('coupons.index'));

        $coupon->refresh();

        $this->assertSame('NEWCODE', $coupon->code);
        $this->assertFalse($coupon->status);
    }

    public function test_coupon_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $coupon = Coupon::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('coupons.destroy', $coupon));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('coupons.index'));

        $this->assertModelMissing($coupon);
    }
}
