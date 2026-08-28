<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
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

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('orders/Index')
                ->has('orders.data', 3),
            );
    }

    public function test_orders_index_is_paginated(): void
    {
        $user = User::factory()->create();
        Order::factory()->count(20)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('orders.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('orders/Index')
                ->has('orders.data', 15)
                ->has('orders.links'),
            );
    }

    public function test_orders_index_can_be_filtered_by_order_status(): void
    {
        $user = User::factory()->create();
        Order::factory()->count(2)->create(['order_status' => 'delivered']);
        Order::factory()->count(3)->create(['order_status' => 'pending']);

        $response = $this
            ->actingAs($user)
            ->get(route('orders.index', ['order_status' => 'delivered']));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('orders/Index')
                ->has('orders.data', 2)
                ->where('filters.order_status', 'delivered'),
            );
    }

    public function test_orders_index_can_be_filtered_by_payment_status(): void
    {
        $user = User::factory()->create();
        Order::factory()->count(2)->create(['payment_status' => 'paid']);
        Order::factory()->count(3)->create(['payment_status' => 'unpaid']);

        $response = $this
            ->actingAs($user)
            ->get(route('orders.index', ['payment_status' => 'paid']));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('orders/Index')
                ->has('orders.data', 2)
                ->where('filters.payment_status', 'paid'),
            );
    }

    public function test_orders_index_can_be_searched_by_order_number_customer_name_or_email(): void
    {
        $user = User::factory()->create();
        Order::factory()->create(['order_number' => 'ORD-FINDME1', 'customer_name' => 'Alice Example']);
        Order::factory()->create(['order_number' => 'ORD-OTHER001', 'customer_name' => 'Bob Example', 'customer_email' => 'findme@example.com']);
        Order::factory()->count(3)->sequence(fn ($sequence) => ['order_number' => "ORD-NOMATCH{$sequence->index}"])->create(['customer_name' => 'Nobody', 'customer_email' => 'nobody@example.com']);

        $response = $this
            ->actingAs($user)
            ->get(route('orders.index', ['search' => 'findme']));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('orders/Index')
                ->has('orders.data', 2)
                ->where('filters.search', 'findme'),
            );
    }

    public function test_order_show_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();
        OrderItem::factory()->count(2)->create(['order_id' => $order->id]);

        $response = $this
            ->actingAs($user)
            ->get(route('orders.show', $order));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('orders/Show')
                ->where('order.id', $order->id)
                ->has('order.items', 2),
            );
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
