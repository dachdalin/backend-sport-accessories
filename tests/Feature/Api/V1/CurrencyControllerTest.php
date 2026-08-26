<?php

namespace Tests\Feature\Api\V1;

use App\Models\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_currencies_are_listed(): void
    {
        Currency::factory()->count(3)->create(['status' => true]);
        Currency::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.currencies.index'));

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'name', 'symbol', 'code', 'exchange_rate']]]);
    }

    public function test_currency_list_is_paginated(): void
    {
        Currency::factory()->count(16)->create(['status' => true]);

        $response = $this->getJson(route('api.v1.currencies.index'));

        $response->assertOk()->assertJsonCount(15, 'data');
    }

    public function test_active_currency_can_be_shown(): void
    {
        $currency = Currency::factory()->create(['status' => true]);

        $response = $this->getJson(route('api.v1.currencies.show', $currency));

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $currency->id)
            ->assertJsonPath('data.code', $currency->code);
    }

    public function test_inactive_currency_is_not_found(): void
    {
        $currency = Currency::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.currencies.show', $currency));

        $response->assertNotFound();
    }

    public function test_missing_currency_is_not_found(): void
    {
        $response = $this->getJson(route('api.v1.currencies.show', 999999));

        $response->assertNotFound();
    }
}
