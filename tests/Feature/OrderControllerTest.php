<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_orders_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Order::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('orders.index'));

        $response->assertOk();
    }

    public function test_order_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('orders.create'));

        $response->assertOk();
    }

    public function test_order_can_be_created(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['unit_price' => '25.00']);

        $response = $this
            ->actingAs($user)
            ->post(route('orders.store'), [
                'customer_name' => 'Jane Doe',
                'customer_email' => 'jane@example.com',
                'shipping_address' => '123 Main St, Springfield',
                'order_status' => 'pending',
                'payment_status' => 'unpaid',
                'shipping_cost' => '5.00',
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 2, 'unit_price' => '25.00'],
                ],
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('orders.index'));

        $order = Order::sole();

        $this->assertSame('Jane Doe', $order->customer_name);
        $this->assertNotEmpty($order->order_number);
        $this->assertSame('55.00', $order->order_amount);
        $this->assertCount(1, $order->items);
        $this->assertSame($product->name, $order->items->first()->product_name);
        $this->assertSame('50.00', $order->items->first()->subtotal);
    }

    public function test_order_customer_name_is_required(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('orders.store'), [
                'customer_name' => '',
                'shipping_address' => '123 Main St',
                'order_status' => 'pending',
                'payment_status' => 'unpaid',
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => '10.00'],
                ],
            ]);

        $response->assertSessionHasErrors('customer_name');
    }

    public function test_order_requires_at_least_one_item(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('orders.store'), [
                'customer_name' => 'Jane Doe',
                'shipping_address' => '123 Main St',
                'order_status' => 'pending',
                'payment_status' => 'unpaid',
                'items' => [],
            ]);

        $response->assertSessionHasErrors('items');
    }

    public function test_order_item_product_must_exist(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('orders.store'), [
                'customer_name' => 'Jane Doe',
                'shipping_address' => '123 Main St',
                'order_status' => 'pending',
                'payment_status' => 'unpaid',
                'items' => [
                    ['product_id' => 999999, 'quantity' => 1, 'unit_price' => '10.00'],
                ],
            ]);

        $response->assertSessionHasErrors('items.0.product_id');
    }

    public function test_order_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('orders.edit', $order));

        $response->assertOk();
    }

    public function test_order_can_be_updated(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['order_status' => 'pending']);
        $product = Product::factory()->create(['unit_price' => '40.00']);

        $response = $this
            ->actingAs($user)
            ->put(route('orders.update', $order), [
                'customer_name' => 'John Smith',
                'shipping_address' => '456 Oak Ave',
                'order_status' => 'shipped',
                'payment_status' => 'paid',
                'shipping_cost' => '0',
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 3, 'unit_price' => '40.00'],
                ],
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('orders.index'));

        $order->refresh();

        $this->assertSame('John Smith', $order->customer_name);
        $this->assertSame('shipped', $order->order_status);
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('120.00', $order->order_amount);
        $this->assertCount(1, $order->items);
    }

    public function test_order_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('orders.destroy', $order));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('orders.index'));

        $this->assertModelMissing($order);
    }

    public function test_guest_cannot_access_orders(): void
    {
        $response = $this->get(route('orders.index'));

        $response->assertRedirect(route('login'));
    }
}
