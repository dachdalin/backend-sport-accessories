<?php

namespace Tests\Feature;

use App\Models\ShippingMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ShippingMethodControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_shipping_methods_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $shippingMethod = ShippingMethod::factory()->create();

        $response = $this->actingAs($user)->get(route('shipping-methods.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('shipping-methods/Index')
                ->has('shippingMethods', 1)
                ->where('shippingMethods.0.id', $shippingMethod->id),
            );
    }

    public function test_guests_cannot_view_shipping_methods(): void
    {
        $response = $this->get(route('shipping-methods.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_shipping_method_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('shipping-methods.create'));

        $response->assertOk();
    }

    public function test_shipping_method_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('shipping-methods.store'), [
            'title' => 'Standard shipping',
            'cost' => '9.99',
            'duration' => '3-5 days',
            'status' => '1',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('shipping-methods.index'));

        $shippingMethod = ShippingMethod::sole();

        $this->assertSame('Standard shipping', $shippingMethod->title);
        $this->assertSame('9.99', $shippingMethod->cost);
        $this->assertSame('3-5 days', $shippingMethod->duration);
        $this->assertTrue($shippingMethod->status);
        $this->assertSame($user->id, $shippingMethod->creator_id);
        $this->assertSame('admin', $shippingMethod->creator_type);
    }

    public function test_shipping_method_title_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('shipping-methods.store'), [
            'title' => '',
            'cost' => '9.99',
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_shipping_method_cost_must_be_a_non_negative_number(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('shipping-methods.store'), [
            'title' => 'Standard shipping',
            'cost' => '-5',
        ]);

        $response->assertSessionHasErrors('cost');
    }

    public function test_shipping_method_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $shippingMethod = ShippingMethod::factory()->create();

        $response = $this->actingAs($user)->get(route('shipping-methods.edit', $shippingMethod));

        $response->assertOk();
    }

    public function test_shipping_method_can_be_updated(): void
    {
        $user = User::factory()->create();
        $shippingMethod = ShippingMethod::factory()->create(['status' => true]);

        $response = $this->actingAs($user)->put(route('shipping-methods.update', $shippingMethod), [
            'title' => 'Express shipping',
            'cost' => '19.99',
            'duration' => '1-2 days',
            'status' => '0',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('shipping-methods.index'));

        $shippingMethod->refresh();

        $this->assertSame('Express shipping', $shippingMethod->title);
        $this->assertSame('19.99', $shippingMethod->cost);
        $this->assertFalse($shippingMethod->status);
    }

    public function test_shipping_method_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $shippingMethod = ShippingMethod::factory()->create();

        $response = $this->actingAs($user)->delete(route('shipping-methods.destroy', $shippingMethod));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('shipping-methods.index'));

        $this->assertModelMissing($shippingMethod);
    }
}
