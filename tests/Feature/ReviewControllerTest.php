<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_reviews_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Review::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('reviews.index'));

        $response->assertOk();
    }

    public function test_review_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('reviews.create'));

        $response->assertOk();
    }

    public function test_review_can_be_created(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('reviews.store'), [
                'product_id' => $product->id,
                'customer_name' => 'Jane Doe',
                'customer_email' => 'jane@example.com',
                'rating' => 4,
                'comment' => 'Great running shoes, very comfortable.',
                'status' => 'pending',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('reviews.index'));

        $review = Review::sole();

        $this->assertSame($product->id, $review->product_id);
        $this->assertSame('Jane Doe', $review->customer_name);
        $this->assertSame(4, $review->rating);
        $this->assertSame('pending', $review->status);
    }

    public function test_review_rating_must_be_between_one_and_five(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('reviews.store'), [
                'product_id' => $product->id,
                'customer_name' => 'Jane Doe',
                'rating' => 6,
                'comment' => 'Too many stars.',
                'status' => 'pending',
            ]);

        $response->assertSessionHasErrors('rating');
    }

    public function test_review_product_must_exist(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('reviews.store'), [
                'product_id' => 999,
                'customer_name' => 'Jane Doe',
                'rating' => 5,
                'comment' => 'Great product.',
                'status' => 'pending',
            ]);

        $response->assertSessionHasErrors('product_id');
    }

    public function test_review_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('reviews.edit', $review));

        $response->assertOk();
    }

    public function test_review_can_be_updated(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['status' => 'pending']);

        $response = $this
            ->actingAs($user)
            ->put(route('reviews.update', $review), [
                'product_id' => $review->product_id,
                'customer_name' => $review->customer_name,
                'rating' => 5,
                'comment' => 'Updated comment after moderation.',
                'admin_reply' => 'Thanks for the feedback!',
                'status' => 'approved',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('reviews.index'));

        $review->refresh();

        $this->assertSame('approved', $review->status);
        $this->assertSame('Thanks for the feedback!', $review->admin_reply);
        $this->assertSame(5, $review->rating);
    }

    public function test_review_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('reviews.destroy', $review));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('reviews.index'));

        $this->assertModelMissing($review);
    }

    public function test_guest_cannot_access_reviews(): void
    {
        $response = $this->get(route('reviews.index'));

        $response->assertRedirect(route('login'));
    }
}
