<?php

namespace Tests\Feature\Api\V1;

use App\Models\Customer;
use App\Models\ShippingAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingAddressControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_shipping_addresses(): void
    {
        $response = $this->getJson(route('api.v1.shipping-addresses.index'));

        $response->assertUnauthorized();
    }

    public function test_authenticated_customer_shipping_addresses_are_listed(): void
    {
        $customer = Customer::factory()->create();
        ShippingAddress::factory()->count(3)->create(['customer_id' => $customer->id]);
        ShippingAddress::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')->getJson(route('api.v1.shipping-addresses.index'));

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'contact_person_name', 'address', 'city', 'country', 'is_default']]]);
    }

    public function test_authenticated_customer_can_create_a_shipping_address(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')
            ->postJson(route('api.v1.shipping-addresses.store'), [
                'contact_person_name' => 'Jane Doe',
                'phone' => '555-1234',
                'address_type' => 'home',
                'address' => '123 Main St',
                'city' => 'Springfield',
                'state' => 'IL',
                'zip' => '62704',
                'country' => 'USA',
                'is_default' => true,
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.contact_person_name', 'Jane Doe')
            ->assertJsonPath('data.is_default', true);

        $this->assertDatabaseHas('shipping_addresses', [
            'customer_id' => $customer->id,
            'contact_person_name' => 'Jane Doe',
        ]);
    }

    public function test_creating_a_default_address_unsets_the_previous_default(): void
    {
        $customer = Customer::factory()->create();
        $existing = ShippingAddress::factory()->create(['customer_id' => $customer->id, 'is_default' => true]);

        $this->actingAs($customer, 'sanctum')
            ->postJson(route('api.v1.shipping-addresses.store'), [
                'contact_person_name' => 'Jane Doe',
                'address_type' => 'home',
                'address' => '123 Main St',
                'city' => 'Springfield',
                'country' => 'USA',
                'is_default' => true,
            ])
            ->assertCreated();

        $this->assertFalse($existing->refresh()->is_default);
    }

    public function test_authenticated_customer_can_update_own_shipping_address(): void
    {
        $customer = Customer::factory()->create();
        $shippingAddress = ShippingAddress::factory()->create(['customer_id' => $customer->id]);

        $response = $this->actingAs($customer, 'sanctum')
            ->putJson(route('api.v1.shipping-addresses.update', $shippingAddress), [
                'contact_person_name' => 'John Smith',
                'address_type' => 'office',
                'address' => '456 Elm St',
                'city' => 'Springfield',
                'country' => 'USA',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.contact_person_name', 'John Smith')
            ->assertJsonPath('data.address_type', 'office');
    }

    public function test_customer_cannot_update_another_customers_shipping_address(): void
    {
        $customer = Customer::factory()->create();
        $shippingAddress = ShippingAddress::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')
            ->putJson(route('api.v1.shipping-addresses.update', $shippingAddress), [
                'contact_person_name' => 'John Smith',
                'address_type' => 'office',
                'address' => '456 Elm St',
                'city' => 'Springfield',
                'country' => 'USA',
            ]);

        $response->assertNotFound();
    }

    public function test_authenticated_customer_can_remove_own_shipping_address(): void
    {
        $customer = Customer::factory()->create();
        $shippingAddress = ShippingAddress::factory()->create(['customer_id' => $customer->id]);

        $response = $this->actingAs($customer, 'sanctum')
            ->deleteJson(route('api.v1.shipping-addresses.destroy', $shippingAddress));

        $response->assertNoContent();
        $this->assertDatabaseMissing('shipping_addresses', ['id' => $shippingAddress->id]);
    }

    public function test_customer_cannot_remove_another_customers_shipping_address(): void
    {
        $customer = Customer::factory()->create();
        $shippingAddress = ShippingAddress::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')
            ->deleteJson(route('api.v1.shipping-addresses.destroy', $shippingAddress));

        $response->assertNotFound();
        $this->assertDatabaseHas('shipping_addresses', ['id' => $shippingAddress->id]);
    }
}
