<?php

namespace Tests\Feature\Api\V1;

use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartItemControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_cart_items(): void
    {
        $response = $this->getJson(route('api.v1.cart-items.index'));

        $response->assertUnauthorized();
    }

    public function test_authenticated_customer_cart_items_are_listed(): void
    {
        $customer = Customer::factory()->create();
        CartItem::factory()->count(3)->create(['customer_id' => $customer->id]);
        CartItem::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')->getJson(route('api.v1.cart-items.index'));

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'product', 'quantity', 'subtotal', 'created_at']]]);
    }

    public function test_authenticated_customer_can_add_product_to_cart(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')
            ->postJson(route('api.v1.cart-items.store'), ['product_id' => $product->id, 'quantity' => 2]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.product.id', $product->id)
            ->assertJsonPath('data.quantity', 2);

        $this->assertDatabaseHas('cart_items', [
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_adding_same_product_again_increments_quantity(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        CartItem::factory()->create(['customer_id' => $customer->id, 'product_id' => $product->id, 'quantity' => 2]);

        $response = $this->actingAs($customer, 'sanctum')
            ->postJson(route('api.v1.cart-items.store'), ['product_id' => $product->id, 'quantity' => 3]);

        $response->assertCreated()->assertJsonPath('data.quantity', 5);

        $this->assertDatabaseCount('cart_items', 1);
        $this->assertDatabaseHas('cart_items', [
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'quantity' => 5,
        ]);
    }

    public function test_quantity_must_be_a_positive_integer(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')
            ->postJson(route('api.v1.cart-items.store'), ['product_id' => $product->id, 'quantity' => 0]);

        $response->assertUnprocessable()->assertJsonValidationErrors('quantity');
    }

    public function test_authenticated_customer_can_remove_own_cart_item(): void
    {
        $customer = Customer::factory()->create();
        $cartItem = CartItem::factory()->create(['customer_id' => $customer->id]);

        $response = $this->actingAs($customer, 'sanctum')
            ->deleteJson(route('api.v1.cart-items.destroy', $cartItem));

        $response->assertNoContent();
        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
    }

    public function test_customer_cannot_remove_another_customers_cart_item(): void
    {
        $customer = Customer::factory()->create();
        $cartItem = CartItem::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')
            ->deleteJson(route('api.v1.cart-items.destroy', $cartItem));

        $response->assertNotFound();
        $this->assertDatabaseHas('cart_items', ['id' => $cartItem->id]);
    }
}
