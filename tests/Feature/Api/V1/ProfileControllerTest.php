<?php

namespace Tests\Feature\Api\V1;

use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Order;
use App\Models\ShippingAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_profile(): void
    {
        $response = $this->getJson(route('api.v1.profile.show'));

        $response->assertUnauthorized();
    }

    public function test_authenticated_customer_can_view_own_profile(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')->getJson(route('api.v1.profile.show'));

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $customer->id)
            ->assertJsonPath('data.email', $customer->email);
    }

    public function test_authenticated_customer_can_update_own_profile(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')->putJson(route('api.v1.profile.update'), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '0123456789',
            'address' => 'New Address',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated Name')
            ->assertJsonPath('data.email', 'updated@example.com');

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '0123456789',
            'address' => 'New Address',
        ]);
    }

    public function test_profile_update_does_not_change_account_status(): void
    {
        $customer = Customer::factory()->create(['status' => false]);

        $this->actingAs($customer, 'sanctum')->putJson(route('api.v1.profile.update'), [
            'name' => 'Updated Name',
            'email' => $customer->email,
        ])->assertOk();

        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'status' => false]);
    }

    public function test_profile_update_rejects_email_already_used_by_another_customer(): void
    {
        $customer = Customer::factory()->create();
        $other = Customer::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')->putJson(route('api.v1.profile.update'), [
            'name' => $customer->name,
            'email' => $other->email,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_profile_update_allows_keeping_the_same_email(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')->putJson(route('api.v1.profile.update'), [
            'name' => 'Same Email Customer',
            'email' => $customer->email,
        ]);

        $response->assertOk()->assertJsonPath('data.email', $customer->email);
    }

    public function test_profile_update_requires_name_and_email(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')->putJson(route('api.v1.profile.update'), []);

        $response->assertUnprocessable()->assertJsonValidationErrors(['name', 'email']);
    }

    public function test_guest_cannot_delete_account(): void
    {
        $response = $this->deleteJson(route('api.v1.profile.destroy'), ['password' => 'password']);

        $response->assertUnauthorized();
    }

    public function test_authenticated_customer_can_delete_own_account_with_correct_password(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')
            ->deleteJson(route('api.v1.profile.destroy'), ['password' => 'password']);

        $response->assertNoContent();
        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }

    public function test_deleting_account_revokes_all_of_the_customers_tokens(): void
    {
        $customer = Customer::factory()->create();
        $customer->createToken('device-1');
        $customer->createToken('device-2');

        $this->actingAs($customer, 'sanctum')
            ->deleteJson(route('api.v1.profile.destroy'), ['password' => 'password'])
            ->assertNoContent();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_deleting_account_cascades_owned_data_but_keeps_order_history(): void
    {
        $customer = Customer::factory()->create();
        $cartItem = CartItem::factory()->create(['customer_id' => $customer->id]);
        $shippingAddress = ShippingAddress::factory()->create(['customer_id' => $customer->id]);
        $order = Order::factory()->create(['customer_id' => $customer->id]);

        $this->actingAs($customer, 'sanctum')
            ->deleteJson(route('api.v1.profile.destroy'), ['password' => 'password'])
            ->assertNoContent();

        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
        $this->assertDatabaseMissing('shipping_addresses', ['id' => $shippingAddress->id]);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'customer_id' => null]);
    }

    public function test_deleting_account_requires_the_correct_password(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')
            ->deleteJson(route('api.v1.profile.destroy'), ['password' => 'wrong-password']);

        $response->assertUnprocessable()->assertJsonValidationErrors('password');
        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
    }

    public function test_deleting_account_requires_a_password(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')->deleteJson(route('api.v1.profile.destroy'), []);

        $response->assertUnprocessable()->assertJsonValidationErrors('password');
        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
    }

    public function test_customer_without_a_password_cannot_delete_their_account(): void
    {
        $customer = Customer::factory()->create(['password' => null]);

        $response = $this->actingAs($customer, 'sanctum')
            ->deleteJson(route('api.v1.profile.destroy'), ['password' => 'anything']);

        $response->assertUnprocessable()->assertJsonValidationErrors('password');
        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
    }
}
