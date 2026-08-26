<?php

namespace Tests\Feature\Api\V1;

use App\Models\OfflinePaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfflinePaymentMethodControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_offline_payment_methods_are_listed(): void
    {
        OfflinePaymentMethod::factory()->count(3)->create(['status' => true]);
        OfflinePaymentMethod::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.offline-payment-methods.index'));

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'method_name', 'method_fields', 'method_informations']]]);
    }

    public function test_offline_payment_method_list_is_paginated(): void
    {
        OfflinePaymentMethod::factory()->count(16)->create(['status' => true]);

        $response = $this->getJson(route('api.v1.offline-payment-methods.index'));

        $response->assertOk()->assertJsonCount(15, 'data');
    }

    public function test_active_offline_payment_method_can_be_shown(): void
    {
        $method = OfflinePaymentMethod::factory()->create(['status' => true]);

        $response = $this->getJson(route('api.v1.offline-payment-methods.show', $method));

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $method->id)
            ->assertJsonPath('data.method_name', $method->method_name);
    }

    public function test_inactive_offline_payment_method_is_not_found(): void
    {
        $method = OfflinePaymentMethod::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.offline-payment-methods.show', $method));

        $response->assertNotFound();
    }

    public function test_missing_offline_payment_method_is_not_found(): void
    {
        $response = $this->getJson(route('api.v1.offline-payment-methods.show', 999999));

        $response->assertNotFound();
    }
}
