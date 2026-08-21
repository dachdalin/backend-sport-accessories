<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_wishlists_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Wishlist::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('wishlists.index'));

        $response->assertOk();
    }

    public function test_wishlist_entry_can_be_created(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('wishlists.store'), [
                'product_id' => $product->id,
                'customer_name' => 'Jane Doe',
                'customer_email' => 'jane@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('wishlists.index'));

        $wishlist = Wishlist::sole();

        $this->assertSame($product->id, $wishlist->product_id);
        $this->assertSame('Jane Doe', $wishlist->customer_name);
        $this->assertSame('jane@example.com', $wishlist->customer_email);
    }

    public function test_wishlist_product_must_exist(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('wishlists.store'), [
                'product_id' => 999,
                'customer_name' => 'Jane Doe',
            ]);

        $response->assertSessionHasErrors('product_id');
    }

    public function test_wishlist_customer_name_is_required(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('wishlists.store'), [
                'product_id' => $product->id,
            ]);

        $response->assertSessionHasErrors('customer_name');
    }

    public function test_wishlist_entry_can_be_updated(): void
    {
        $user = User::factory()->create();
        $wishlist = Wishlist::factory()->create();
        $product = Product::factory()->create();

        $response = $this
            ->actingAs($user)
            ->put(route('wishlists.update', $wishlist), [
                'product_id' => $product->id,
                'customer_name' => 'John Smith',
                'customer_email' => 'john@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('wishlists.index'));

        $wishlist->refresh();

        $this->assertSame($product->id, $wishlist->product_id);
        $this->assertSame('John Smith', $wishlist->customer_name);
        $this->assertSame('john@example.com', $wishlist->customer_email);
    }

    public function test_wishlist_entry_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $wishlist = Wishlist::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('wishlists.destroy', $wishlist));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('wishlists.index'));

        $this->assertModelMissing($wishlist);
    }

    public function test_guest_cannot_access_wishlists(): void
    {
        $response = $this->get(route('wishlists.index'));

        $response->assertRedirect(route('login'));
    }
}
