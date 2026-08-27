<?php

namespace Tests\Feature\Api\V1;

use App\Enums\CouponType;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingAddress;
use App\Models\ShippingMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_orders(): void
    {
        $response = $this->getJson(route('api.v1.orders.index'));

        $response->assertUnauthorized();
    }

    public function test_authenticated_customer_can_process_an_order_from_their_cart(): void
    {
        $customer = Customer::factory()->create();
        $shippingAddress = ShippingAddress::factory()->create(['customer_id' => $customer->id]);
        $shippingMethod = ShippingMethod::factory()->create(['cost' => 15, 'status' => true]);
        $product = Product::factory()->create(['unit_price' => 50, 'discount' => 0, 'current_stock' => 10]);
        CartItem::factory()->create(['customer_id' => $customer->id, 'product_id' => $product->id, 'quantity' => 2]);

        $response = $this->actingAs($customer, 'sanctum')->postJson(route('api.v1.orders.store'), [
            'shipping_address_id' => $shippingAddress->id,
            'shipping_method_id' => $shippingMethod->id,
            'payment_method' => 'cod',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.shipping_cost', '15.00')
            ->assertJsonPath('data.order_amount', '115.00')
            ->assertJsonPath('data.items.0.product_id', $product->id)
            ->assertJsonPath('data.items.0.quantity', 2);

        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'order_amount' => 115.00,
            'payment_method' => 'cod',
        ]);
        $this->assertDatabaseCount('order_items', 1);
        $this->assertDatabaseCount('cart_items', 0);
        $this->assertSame(8, $product->fresh()->current_stock);
    }

    public function test_order_fails_when_cart_is_empty(): void
    {
        $customer = Customer::factory()->create();
        $shippingAddress = ShippingAddress::factory()->create(['customer_id' => $customer->id]);

        $response = $this->actingAs($customer, 'sanctum')->postJson(route('api.v1.orders.store'), [
            'shipping_address_id' => $shippingAddress->id,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('cart');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_order_fails_when_stock_is_insufficient(): void
    {
        $customer = Customer::factory()->create();
        $shippingAddress = ShippingAddress::factory()->create(['customer_id' => $customer->id]);
        $product = Product::factory()->create(['current_stock' => 1]);
        CartItem::factory()->create(['customer_id' => $customer->id, 'product_id' => $product->id, 'quantity' => 5]);

        $response = $this->actingAs($customer, 'sanctum')->postJson(route('api.v1.orders.store'), [
            'shipping_address_id' => $shippingAddress->id,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('cart');
        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(1, $product->fresh()->current_stock);
    }

    public function test_order_cannot_use_another_customers_shipping_address(): void
    {
        $customer = Customer::factory()->create();
        $shippingAddress = ShippingAddress::factory()->create();
        $product = Product::factory()->create(['current_stock' => 5]);
        CartItem::factory()->create(['customer_id' => $customer->id, 'product_id' => $product->id, 'quantity' => 1]);

        $response = $this->actingAs($customer, 'sanctum')->postJson(route('api.v1.orders.store'), [
            'shipping_address_id' => $shippingAddress->id,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('shipping_address_id');
    }

    public function test_order_applies_a_valid_coupon_discount(): void
    {
        $customer = Customer::factory()->create();
        $shippingAddress = ShippingAddress::factory()->create(['customer_id' => $customer->id]);
        $product = Product::factory()->create(['unit_price' => 100, 'discount' => 0, 'current_stock' => 5]);
        CartItem::factory()->create(['customer_id' => $customer->id, 'product_id' => $product->id, 'quantity' => 1]);
        Coupon::factory()->create([
            'code' => 'SAVE10',
            'type' => CouponType::Fixed,
            'value' => 10,
            'min_order_amount' => null,
        ]);

        $response = $this->actingAs($customer, 'sanctum')->postJson(route('api.v1.orders.store'), [
            'shipping_address_id' => $shippingAddress->id,
            'coupon_code' => 'save10',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.discount_amount', '10.00')
            ->assertJsonPath('data.order_amount', '90.00');
    }

    public function test_order_rejects_an_invalid_coupon_code(): void
    {
        $customer = Customer::factory()->create();
        $shippingAddress = ShippingAddress::factory()->create(['customer_id' => $customer->id]);
        $product = Product::factory()->create(['current_stock' => 5]);
        CartItem::factory()->create(['customer_id' => $customer->id, 'product_id' => $product->id, 'quantity' => 1]);

        $response = $this->actingAs($customer, 'sanctum')->postJson(route('api.v1.orders.store'), [
            'shipping_address_id' => $shippingAddress->id,
            'coupon_code' => 'MISSING',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('coupon_code');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_authenticated_customer_orders_are_listed(): void
    {
        $customer = Customer::factory()->create();
        Order::factory()->count(2)->create(['customer_id' => $customer->id]);
        Order::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')->getJson(route('api.v1.orders.index'));

        $response->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_customer_cannot_view_another_customers_order(): void
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')->getJson(route('api.v1.orders.show', $order));

        $response->assertNotFound();
    }
}
