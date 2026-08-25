<?php

namespace Tests\Feature\Api\V1;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_products_are_listed(): void
    {
        Product::factory()->count(3)->create(['status' => true]);
        Product::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.products.index'));

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'name', 'slug', 'thumbnail_url', 'unit_price']]]);
    }

    public function test_product_list_is_paginated(): void
    {
        Product::factory()->count(16)->create(['status' => true]);

        $response = $this->getJson(route('api.v1.products.index'));

        $response->assertOk()->assertJsonCount(15, 'data');
    }

    public function test_active_product_can_be_shown(): void
    {
        $product = Product::factory()->create(['status' => true]);
        ProductImage::query()->create([
            'product_id' => $product->id,
            'image' => 'product.png',
        ]);

        $response = $this->getJson(route('api.v1.products.show', $product));

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.name', $product->name)
            ->assertJsonCount(1, 'data.images');
    }

    public function test_inactive_product_is_not_found(): void
    {
        $product = Product::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.products.show', $product));

        $response->assertNotFound();
    }

    public function test_missing_product_is_not_found(): void
    {
        $response = $this->getJson(route('api.v1.products.show', 999999));

        $response->assertNotFound();
    }
}
