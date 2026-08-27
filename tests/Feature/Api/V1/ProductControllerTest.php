<?php

namespace Tests\Feature\Api\V1;

use App\Enums\ReviewStatus;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Review;
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

    public function test_discounted_filter_lists_only_products_with_a_discount(): void
    {
        Product::factory()->create(['status' => true, 'discount' => 10, 'discount_type' => 'percent']);
        Product::factory()->create(['status' => true, 'discount' => 0]);

        $response = $this->getJson(route('api.v1.products.index', ['discounted' => 1]));

        $response->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_discounted_false_filter_lists_only_products_without_a_discount(): void
    {
        Product::factory()->create(['status' => true, 'discount' => 10, 'discount_type' => 'percent']);
        $undiscounted = Product::factory()->create(['status' => true, 'discount' => 0]);

        $response = $this->getJson(route('api.v1.products.index', ['discounted' => 0]));

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $undiscounted->id);
    }

    public function test_in_stock_true_filter_lists_only_products_with_stock(): void
    {
        $inStock = Product::factory()->create(['status' => true, 'current_stock' => 5]);
        Product::factory()->create(['status' => true, 'current_stock' => 0]);

        $response = $this->getJson(route('api.v1.products.index', ['in_stock' => 1]));

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $inStock->id);
    }

    public function test_in_stock_false_filter_lists_only_out_of_stock_products(): void
    {
        Product::factory()->create(['status' => true, 'current_stock' => 5]);
        $outOfStock = Product::factory()->create(['status' => true, 'current_stock' => 0]);

        $response = $this->getJson(route('api.v1.products.index', ['in_stock' => 0]));

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $outOfStock->id);
    }

    public function test_search_filter_matches_product_name(): void
    {
        $match = Product::factory()->create(['status' => true, 'name' => 'Pro Training Football']);
        Product::factory()->create(['status' => true, 'name' => 'Tennis Racket']);

        $response = $this->getJson(route('api.v1.products.index', ['search' => 'football']));

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match->id);
    }

    public function test_search_filter_matches_product_code(): void
    {
        $match = Product::factory()->create(['status' => true, 'code' => 'SKU-9911']);
        Product::factory()->create(['status' => true, 'code' => 'SKU-1234']);

        $response = $this->getJson(route('api.v1.products.index', ['search' => '9911']));

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match->id);
    }

    public function test_price_range_filter_limits_results(): void
    {
        Product::factory()->create(['status' => true, 'unit_price' => 10]);
        $inRange = Product::factory()->create(['status' => true, 'unit_price' => 50]);
        Product::factory()->create(['status' => true, 'unit_price' => 200]);

        $response = $this->getJson(route('api.v1.products.index', ['min_price' => 20, 'max_price' => 100]));

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $inRange->id);
    }

    public function test_max_price_must_be_greater_than_or_equal_to_min_price(): void
    {
        $response = $this->getJson(route('api.v1.products.index', ['min_price' => 100, 'max_price' => 10]));

        $response->assertUnprocessable()->assertJsonValidationErrors('max_price');
    }

    public function test_rating_filter_lists_only_products_meeting_the_minimum_average_rating(): void
    {
        $highRated = Product::factory()->create(['status' => true]);
        Review::factory()->create(['product_id' => $highRated->id, 'rating' => 5, 'status' => ReviewStatus::Approved]);
        Review::factory()->create(['product_id' => $highRated->id, 'rating' => 4, 'status' => ReviewStatus::Approved]);

        $lowRated = Product::factory()->create(['status' => true]);
        Review::factory()->create(['product_id' => $lowRated->id, 'rating' => 2, 'status' => ReviewStatus::Approved]);

        $response = $this->getJson(route('api.v1.products.index', ['rating' => 4]));

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $highRated->id)
            ->assertJsonPath('data.0.average_rating', 4.5);
    }

    public function test_rating_filter_ignores_unapproved_reviews(): void
    {
        $product = Product::factory()->create(['status' => true]);
        Review::factory()->create(['product_id' => $product->id, 'rating' => 5, 'status' => ReviewStatus::Pending]);

        $response = $this->getJson(route('api.v1.products.index', ['rating' => 1]));

        $response->assertOk()->assertJsonCount(0, 'data');
    }

    public function test_rating_must_be_within_range(): void
    {
        $response = $this->getJson(route('api.v1.products.index', ['rating' => 6]));

        $response->assertUnprocessable()->assertJsonValidationErrors('rating');
    }

    public function test_final_price_applies_percent_discount(): void
    {
        $product = Product::factory()->create([
            'status' => true,
            'unit_price' => 100,
            'discount' => 10,
            'discount_type' => 'percent',
        ]);

        $response = $this->getJson(route('api.v1.products.show', $product));

        $response->assertOk()->assertJsonPath('data.final_price', 90);
    }

    public function test_final_price_applies_flat_amount_discount(): void
    {
        $product = Product::factory()->create([
            'status' => true,
            'unit_price' => 100,
            'discount' => 15,
            'discount_type' => 'amount',
        ]);

        $response = $this->getJson(route('api.v1.products.show', $product));

        $response->assertOk()->assertJsonPath('data.final_price', 85);
    }

    public function test_final_price_matches_unit_price_when_no_discount(): void
    {
        $product = Product::factory()->create([
            'status' => true,
            'unit_price' => 100,
            'discount' => 0,
        ]);

        $response = $this->getJson(route('api.v1.products.show', $product));

        $response->assertOk()->assertJsonPath('data.final_price', 100);
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
