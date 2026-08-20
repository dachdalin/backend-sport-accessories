<?php

namespace Tests\Feature;

use App\Models\StoreLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreLocationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_locations_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        StoreLocation::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('store-locations.index'));

        $response->assertOk();
    }

    public function test_store_location_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('store-locations.create'));

        $response->assertOk();
    }

    public function test_store_location_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('store-locations.store'), [
                'name' => 'Downtown Flagship Store',
                'address' => '123 Main Street',
                'city' => 'Bangkok',
                'phone' => '+66 2 123 4567',
                'opening_hours' => '9:00 AM - 9:00 PM',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('store-locations.index'));

        $storeLocation = StoreLocation::sole();

        $this->assertSame('Downtown Flagship Store', $storeLocation->name);
        $this->assertSame('Bangkok', $storeLocation->city);
        $this->assertTrue($storeLocation->status);
    }

    public function test_store_location_name_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('store-locations.store'), [
                'name' => '',
                'address' => '123 Main Street',
                'city' => 'Bangkok',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_store_location_address_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('store-locations.store'), [
                'name' => 'Downtown Store',
                'address' => '',
                'city' => 'Bangkok',
            ]);

        $response->assertSessionHasErrors('address');
    }

    public function test_store_location_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $storeLocation = StoreLocation::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('store-locations.edit', $storeLocation));

        $response->assertOk();
    }

    public function test_store_location_can_be_updated(): void
    {
        $user = User::factory()->create();
        $storeLocation = StoreLocation::factory()->create(['status' => true]);

        $response = $this
            ->actingAs($user)
            ->put(route('store-locations.update', $storeLocation), [
                'name' => 'Uptown Branch',
                'address' => $storeLocation->address,
                'city' => $storeLocation->city,
                'phone' => $storeLocation->phone,
                'opening_hours' => $storeLocation->opening_hours,
                'status' => '0',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('store-locations.index'));

        $storeLocation->refresh();

        $this->assertSame('Uptown Branch', $storeLocation->name);
        $this->assertFalse($storeLocation->status);
    }

    public function test_store_location_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $storeLocation = StoreLocation::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('store-locations.destroy', $storeLocation));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('store-locations.index'));

        $this->assertModelMissing($storeLocation);
    }
}
