<?php

namespace Tests\Feature\Api\V1;

use App\Models\ShippingMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingMethodControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_shipping_methods_are_listed(): void
    {
        ShippingMethod::factory()->count(3)->create(['status' => true]);
        ShippingMethod::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.shipping-methods.index'));

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'title', 'cost', 'duration']]]);
    }

    public function test_shipping_method_list_is_paginated(): void
    {
        ShippingMethod::factory()->count(16)->create(['status' => true]);

        $response = $this->getJson(route('api.v1.shipping-methods.index'));

        $response->assertOk()->assertJsonCount(15, 'data');
    }

    public function test_active_shipping_method_can_be_shown(): void
    {
        $shippingMethod = ShippingMethod::factory()->create(['status' => true]);

        $response = $this->getJson(route('api.v1.shipping-methods.show', $shippingMethod));

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $shippingMethod->id)
            ->assertJsonPath('data.title', $shippingMethod->title);
    }

    public function test_inactive_shipping_method_is_not_found(): void
    {
        $shippingMethod = ShippingMethod::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.shipping-methods.show', $shippingMethod));

        $response->assertNotFound();
    }

    public function test_missing_shipping_method_is_not_found(): void
    {
        $response = $this->getJson(route('api.v1.shipping-methods.show', 999999));

        $response->assertNotFound();
    }
}
