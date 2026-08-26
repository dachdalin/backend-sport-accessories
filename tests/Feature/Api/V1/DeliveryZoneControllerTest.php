<?php

namespace Tests\Feature\Api\V1;

use App\Models\DeliveryZone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryZoneControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_delivery_zones_are_listed(): void
    {
        DeliveryZone::factory()->count(3)->create(['status' => true]);
        DeliveryZone::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.delivery-zones.index'));

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'zip_code', 'city', 'delivery_charge']]]);
    }

    public function test_delivery_zone_list_is_paginated(): void
    {
        DeliveryZone::factory()->count(16)->create(['status' => true]);

        $response = $this->getJson(route('api.v1.delivery-zones.index'));

        $response->assertOk()->assertJsonCount(15, 'data');
    }

    public function test_active_delivery_zone_can_be_shown(): void
    {
        $deliveryZone = DeliveryZone::factory()->create(['status' => true]);

        $response = $this->getJson(route('api.v1.delivery-zones.show', $deliveryZone));

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $deliveryZone->id)
            ->assertJsonPath('data.zip_code', $deliveryZone->zip_code);
    }

    public function test_inactive_delivery_zone_is_not_found(): void
    {
        $deliveryZone = DeliveryZone::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.delivery-zones.show', $deliveryZone));

        $response->assertNotFound();
    }

    public function test_missing_delivery_zone_is_not_found(): void
    {
        $response = $this->getJson(route('api.v1.delivery-zones.show', 999999));

        $response->assertNotFound();
    }
}
