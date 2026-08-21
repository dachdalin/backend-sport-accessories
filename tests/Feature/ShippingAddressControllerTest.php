<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\ShippingAddress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingAddressControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_shipping_addresses_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        ShippingAddress::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('shipping-addresses.index'));

        $response->assertOk();
    }

    public function test_shipping_address_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('shipping-addresses.create'));

        $response->assertOk();
    }

    public function test_shipping_address_can_be_created(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('shipping-addresses.store'), [
                'customer_id' => $customer->id,
                'contact_person_name' => 'Jane Doe',
                'phone' => '5550100',
                'address_type' => 'home',
                'address' => '123 Main St',
                'city' => 'Springfield',
                'state' => 'IL',
                'zip' => '62701',
                'country' => 'USA',
                'is_default' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('shipping-addresses.index'));

        $shippingAddress = ShippingAddress::sole();

        $this->assertSame($customer->id, $shippingAddress->customer_id);
        $this->assertSame('Jane Doe', $shippingAddress->contact_person_name);
        $this->assertTrue($shippingAddress->is_default);
    }

    public function test_shipping_address_customer_must_exist(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('shipping-addresses.store'), [
                'customer_id' => 999,
                'contact_person_name' => 'Jane Doe',
                'address_type' => 'home',
                'address' => '123 Main St',
                'city' => 'Springfield',
                'country' => 'USA',
            ]);

        $response->assertSessionHasErrors('customer_id');
    }

    public function test_only_one_default_address_per_customer(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();
        $existingDefault = ShippingAddress::factory()->create([
            'customer_id' => $customer->id,
            'is_default' => true,
        ]);

        $this
            ->actingAs($user)
            ->post(route('shipping-addresses.store'), [
                'customer_id' => $customer->id,
                'contact_person_name' => 'New Address',
                'address_type' => 'office',
                'address' => '456 Office Rd',
                'city' => 'Springfield',
                'country' => 'USA',
                'is_default' => '1',
            ])
            ->assertSessionHasNoErrors();

        $this->assertFalse($existingDefault->fresh()->is_default);
        $this->assertTrue(ShippingAddress::where('contact_person_name', 'New Address')->sole()->is_default);
    }

    public function test_shipping_address_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $shippingAddress = ShippingAddress::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('shipping-addresses.edit', $shippingAddress));

        $response->assertOk();
    }

    public function test_shipping_address_can_be_updated(): void
    {
        $user = User::factory()->create();
        $shippingAddress = ShippingAddress::factory()->create();

        $response = $this
            ->actingAs($user)
            ->put(route('shipping-addresses.update', $shippingAddress), [
                'customer_id' => $shippingAddress->customer_id,
                'contact_person_name' => 'Updated Name',
                'address_type' => 'office',
                'address' => '789 Updated Ave',
                'city' => 'Metropolis',
                'country' => 'USA',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('shipping-addresses.index'));

        $shippingAddress->refresh();

        $this->assertSame('Updated Name', $shippingAddress->contact_person_name);
        $this->assertSame('789 Updated Ave', $shippingAddress->address);
        $this->assertSame('office', $shippingAddress->address_type);
    }

    public function test_shipping_address_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $shippingAddress = ShippingAddress::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('shipping-addresses.destroy', $shippingAddress));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('shipping-addresses.index'));

        $this->assertModelMissing($shippingAddress);
    }

    public function test_guest_cannot_access_shipping_addresses(): void
    {
        $response = $this->get(route('shipping-addresses.index'));

        $response->assertRedirect(route('login'));
    }
}
