<?php

namespace Tests\Feature\Api\V1;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_wishlists(): void
    {
        $response = $this->getJson(route('api.v1.wishlists.index'));

        $response->assertUnauthorized();
    }

    public function test_authenticated_customer_wishlist_entries_are_listed(): void
    {
        $customer = Customer::factory()->create();
        Wishlist::factory()->count(3)->create(['customer_email' => $customer->email]);
        Wishlist::factory()->create(['customer_email' => 'someone-else@example.com']);

        $response = $this->actingAs($customer, 'sanctum')->getJson(route('api.v1.wishlists.index'));

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'product', 'created_at']]]);
    }

    public function test_authenticated_customer_can_add_product_to_wishlist(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')
            ->postJson(route('api.v1.wishlists.store'), ['product_id' => $product->id]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.product.id', $product->id);

        $this->assertDatabaseHas('wishlists', [
            'product_id' => $product->id,
            'customer_email' => $customer->email,
        ]);
    }

    public function test_customer_cannot_add_same_product_twice(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        Wishlist::factory()->create(['product_id' => $product->id, 'customer_email' => $customer->email]);

        $response = $this->actingAs($customer, 'sanctum')
            ->postJson(route('api.v1.wishlists.store'), ['product_id' => $product->id]);

        $response->assertUnprocessable()->assertJsonValidationErrors('product_id');
    }

    public function test_authenticated_customer_can_remove_own_wishlist_entry(): void
    {
        $customer = Customer::factory()->create();
        $wishlist = Wishlist::factory()->create(['customer_email' => $customer->email]);

        $response = $this->actingAs($customer, 'sanctum')
            ->deleteJson(route('api.v1.wishlists.destroy', $wishlist));

        $response->assertNoContent();
        $this->assertDatabaseMissing('wishlists', ['id' => $wishlist->id]);
    }

    public function test_customer_cannot_remove_another_customers_wishlist_entry(): void
    {
        $customer = Customer::factory()->create();
        $wishlist = Wishlist::factory()->create(['customer_email' => 'someone-else@example.com']);

        $response = $this->actingAs($customer, 'sanctum')
            ->deleteJson(route('api.v1.wishlists.destroy', $wishlist));

        $response->assertNotFound();
        $this->assertDatabaseHas('wishlists', ['id' => $wishlist->id]);
    }
}
