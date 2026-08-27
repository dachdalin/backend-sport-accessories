<?php

namespace Tests\Feature\Api\V1;

use App\Models\Product;
use App\Models\ProductSearch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrendingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_trending_products_are_ranked_by_this_weeks_search_count(): void
    {
        $mostSearched = Product::factory()->create(['status' => true]);
        $leastSearched = Product::factory()->create(['status' => true]);

        ProductSearch::factory()->count(5)->create(['product_id' => $mostSearched->id, 'created_at' => now()]);
        ProductSearch::factory()->count(2)->create(['product_id' => $leastSearched->id, 'created_at' => now()]);

        $response = $this->getJson(route('api.v1.trending'));

        $response
            ->assertOk()
            ->assertJsonPath('data.0.product.id', $mostSearched->id)
            ->assertJsonPath('data.0.search_count', 5)
            ->assertJsonPath('data.1.product.id', $leastSearched->id)
            ->assertJsonPath('data.1.search_count', 2)
            ->assertJsonPath('data.0.rank', 1)
            ->assertJsonPath('data.1.rank', 2);
    }

    public function test_growth_percent_compares_this_week_to_the_week_before(): void
    {
        $product = Product::factory()->create(['status' => true]);

        ProductSearch::factory()->count(4)->create(['product_id' => $product->id, 'created_at' => now()->subDays(10)]);
        ProductSearch::factory()->count(8)->create(['product_id' => $product->id, 'created_at' => now()]);

        $response = $this->getJson(route('api.v1.trending'));

        $response->assertOk()->assertJsonPath('data.0.growth_percent', 100);
    }

    public function test_growth_percent_is_null_without_a_prior_week_baseline(): void
    {
        $product = Product::factory()->create(['status' => true]);

        ProductSearch::factory()->count(3)->create(['product_id' => $product->id, 'created_at' => now()]);

        $response = $this->getJson(route('api.v1.trending'));

        $response->assertOk()->assertJsonPath('data.0.growth_percent', null);
    }

    public function test_searches_older_than_two_weeks_are_ignored(): void
    {
        $product = Product::factory()->create(['status' => true]);

        ProductSearch::factory()->count(9)->create(['product_id' => $product->id, 'created_at' => now()->subDays(30)]);

        $response = $this->getJson(route('api.v1.trending'));

        $response->assertOk()->assertJsonCount(0, 'data');
    }

    public function test_inactive_products_are_excluded(): void
    {
        $product = Product::factory()->create(['status' => false]);

        ProductSearch::factory()->count(5)->create(['product_id' => $product->id, 'created_at' => now()]);

        $response = $this->getJson(route('api.v1.trending'));

        $response->assertOk()->assertJsonCount(0, 'data');
    }

    public function test_limit_caps_the_number_of_results(): void
    {
        Product::factory()->count(3)->create(['status' => true])->each(
            fn (Product $product) => ProductSearch::factory()->count(1)->create(['product_id' => $product->id, 'created_at' => now()]),
        );

        $response = $this->getJson(route('api.v1.trending', ['limit' => 2]));

        $response->assertOk()->assertJsonCount(2, 'data');
    }
}
