<?php

namespace Tests\Feature\Api\V1;

use App\Models\StoreLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreLocationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_store_locations_are_listed(): void
    {
        StoreLocation::factory()->count(3)->create(['status' => true]);
        StoreLocation::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.store-locations.index'));

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'name', 'address', 'city', 'phone', 'opening_hours']]]);
    }

    public function test_store_location_list_is_paginated(): void
    {
        StoreLocation::factory()->count(16)->create(['status' => true]);

        $response = $this->getJson(route('api.v1.store-locations.index'));

        $response->assertOk()->assertJsonCount(15, 'data');
    }

    public function test_active_store_location_can_be_shown(): void
    {
        $storeLocation = StoreLocation::factory()->create(['status' => true]);

        $response = $this->getJson(route('api.v1.store-locations.show', $storeLocation));

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $storeLocation->id)
            ->assertJsonPath('data.name', $storeLocation->name);
    }

    public function test_inactive_store_location_is_not_found(): void
    {
        $storeLocation = StoreLocation::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.store-locations.show', $storeLocation));

        $response->assertNotFound();
    }

    public function test_missing_store_location_is_not_found(): void
    {
        $response = $this->getJson(route('api.v1.store-locations.show', 999999));

        $response->assertNotFound();
    }
}
