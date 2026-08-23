<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Product::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('products.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('products/Index')
                ->has('products.data', 3),
            );
    }

    public function test_product_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('products.create'));

        $response->assertOk();
    }

    public function test_product_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('products.store'), [
                'name' => 'Running Shoes Pro',
                'unit_price' => '99.99',
                'current_stock' => 50,
                'minimum_order_qty' => 1,
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('products.index'));

        $product = Product::sole();

        $this->assertSame('Running Shoes Pro', $product->name);
        $this->assertSame('running-shoes-pro', $product->slug);
        $this->assertSame('99.99', $product->unit_price);
        $this->assertSame('def.png', $product->thumbnail);
        $this->assertTrue($product->status);
    }

    public function test_product_name_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('products.store'), [
                'name' => '',
                'unit_price' => '10.00',
                'current_stock' => 0,
                'minimum_order_qty' => 1,
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_product_unit_price_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('products.store'), [
                'name' => 'Test Product',
                'unit_price' => '',
                'current_stock' => 0,
                'minimum_order_qty' => 1,
            ]);

        $response->assertSessionHasErrors('unit_price');
    }

    public function test_product_name_must_be_unique(): void
    {
        $user = User::factory()->create();
        Product::factory()->create(['name' => 'Existing Product']);

        $response = $this
            ->actingAs($user)
            ->post(route('products.store'), [
                'name' => 'Existing Product',
                'unit_price' => '10.00',
                'current_stock' => 0,
                'minimum_order_qty' => 1,
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_product_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('products.edit', $product));

        $response->assertOk();
    }

    public function test_product_can_be_updated(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['status' => true]);

        $response = $this
            ->actingAs($user)
            ->put(route('products.update', $product), [
                'name' => 'Updated Shoes',
                'unit_price' => '149.99',
                'current_stock' => 25,
                'minimum_order_qty' => 2,
                'status' => '0',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('products.index'));

        $product->refresh();

        $this->assertSame('Updated Shoes', $product->name);
        $this->assertSame('updated-shoes', $product->slug);
        $this->assertSame('149.99', $product->unit_price);
        $this->assertSame(25, $product->current_stock);
        $this->assertFalse($product->status);
    }

    public function test_product_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('products.destroy', $product));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('products.index'));

        $this->assertModelMissing($product);
    }

    public function test_guest_cannot_access_products(): void
    {
        $response = $this->get(route('products.index'));

        $response->assertRedirect(route('login'));
    }
}
