<?php

namespace Tests\Feature\Api\V1;

use App\Models\Customer;
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
}
