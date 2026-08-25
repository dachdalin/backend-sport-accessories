<?php

namespace Tests\Feature\Api\V1;

use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_brands_are_listed(): void
    {
        Brand::factory()->count(3)->create(['status' => true]);
        Brand::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.brands.index'));

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'name', 'image_url', 'image_alt_text']]]);
    }

    public function test_brand_list_is_paginated(): void
    {
        Brand::factory()->count(16)->create(['status' => true]);

        $response = $this->getJson(route('api.v1.brands.index'));

        $response->assertOk()->assertJsonCount(15, 'data');
    }

    public function test_active_brand_can_be_shown(): void
    {
        $brand = Brand::factory()->create(['status' => true]);

        $response = $this->getJson(route('api.v1.brands.show', $brand));

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $brand->id)
            ->assertJsonPath('data.name', $brand->name);
    }

    public function test_inactive_brand_is_not_found(): void
    {
        $brand = Brand::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.brands.show', $brand));

        $response->assertNotFound();
    }

    public function test_missing_brand_is_not_found(): void
    {
        $response = $this->getJson(route('api.v1.brands.show', 999999));

        $response->assertNotFound();
    }
}
