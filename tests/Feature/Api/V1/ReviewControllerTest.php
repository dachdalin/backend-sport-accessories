<?php

namespace Tests\Feature\Api\V1;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_reviews_are_listed_for_a_product(): void
    {
        $product = Product::factory()->create(['status' => true]);
        Review::factory()->count(2)->create(['product_id' => $product->id, 'status' => 'approved']);
        Review::factory()->create(['product_id' => $product->id, 'status' => 'pending']);

        $response = $this->getJson(route('api.v1.products.reviews.index', $product));

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure(['data' => [['id', 'customer_name', 'rating', 'comment']]]);
    }

    public function test_reviews_for_inactive_product_are_not_found(): void
    {
        $product = Product::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.products.reviews.index', $product));

        $response->assertNotFound();
    }

    public function test_authenticated_customer_can_submit_a_review(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['status' => true]);

        $response = $this->actingAs($customer, 'sanctum')
            ->postJson(route('api.v1.products.reviews.store', $product), [
                'rating' => 5,
                'comment' => 'Great product, would buy again.',
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.customer_name', $customer->name)
            ->assertJsonPath('data.rating', 5);

        $this->assertDatabaseHas('reviews', [
            'product_id' => $product->id,
            'customer_email' => $customer->email,
            'status' => 'pending',
        ]);
    }

    public function test_guest_cannot_submit_a_review(): void
    {
        $product = Product::factory()->create(['status' => true]);

        $response = $this->postJson(route('api.v1.products.reviews.store', $product), [
            'rating' => 5,
            'comment' => 'Great product.',
        ]);

        $response->assertUnauthorized();
    }

    public function test_review_submission_requires_a_valid_rating(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['status' => true]);

        $response = $this->actingAs($customer, 'sanctum')
            ->postJson(route('api.v1.products.reviews.store', $product), [
                'rating' => 6,
                'comment' => 'Great product.',
            ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('rating');
    }
}
