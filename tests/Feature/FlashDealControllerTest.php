<?php

namespace Tests\Feature;

use App\Models\FlashDeal;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashDealControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_flash_deals_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        FlashDeal::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('flash-deals.index'));

        $response->assertOk();
    }

    public function test_flash_deal_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('flash-deals.create'));

        $response->assertOk();
    }

    public function test_flash_deal_can_be_created(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('flash-deals.store'), [
                'title' => 'Weekend Sports Sale',
                'start_date' => '2026-09-01',
                'end_date' => '2026-09-07',
                'featured' => '1',
                'status' => '1',
                'items' => [
                    ['product_id' => $product->id, 'discount' => 15, 'discount_type' => 'percent'],
                ],
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('flash-deals.index'));

        $flashDeal = FlashDeal::sole();

        $this->assertSame('Weekend Sports Sale', $flashDeal->title);
        $this->assertSame('weekend-sports-sale', $flashDeal->slug);
        $this->assertTrue($flashDeal->featured);
        $this->assertTrue($flashDeal->status);
        $this->assertSame('def.png', $flashDeal->banner);
        $this->assertCount(1, $flashDeal->items);
        $this->assertSame($product->id, $flashDeal->items->first()->product_id);
    }

    public function test_flash_deal_slug_is_made_unique_when_titles_collide(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        FlashDeal::factory()->create(['title' => 'Weekend Sports Sale', 'slug' => 'weekend-sports-sale']);

        $response = $this
            ->actingAs($user)
            ->post(route('flash-deals.store'), [
                'title' => 'Weekend Sports Sale',
                'start_date' => '2026-09-01',
                'end_date' => '2026-09-07',
                'items' => [
                    ['product_id' => $product->id, 'discount' => 10, 'discount_type' => 'amount'],
                ],
            ]);

        $response->assertSessionHasNoErrors();

        $this->assertSame('weekend-sports-sale-1', FlashDeal::latest('id')->first()->slug);
    }

    public function test_flash_deal_end_date_must_be_after_or_equal_start_date(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('flash-deals.store'), [
                'title' => 'Bad Dates',
                'start_date' => '2026-09-10',
                'end_date' => '2026-09-01',
                'items' => [
                    ['product_id' => $product->id, 'discount' => 10, 'discount_type' => 'percent'],
                ],
            ]);

        $response->assertSessionHasErrors('end_date');
    }

    public function test_flash_deal_requires_at_least_one_item(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('flash-deals.store'), [
                'title' => 'No Products',
                'start_date' => '2026-09-01',
                'end_date' => '2026-09-07',
                'items' => [],
            ]);

        $response->assertSessionHasErrors('items');
    }

    public function test_flash_deal_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $flashDeal = FlashDeal::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('flash-deals.edit', $flashDeal));

        $response->assertOk();
    }

    public function test_flash_deal_can_be_updated(): void
    {
        $user = User::factory()->create();
        $flashDeal = FlashDeal::factory()->create(['title' => 'Old title']);
        $product = Product::factory()->create();

        $response = $this
            ->actingAs($user)
            ->put(route('flash-deals.update', $flashDeal), [
                'title' => 'New title',
                'start_date' => '2026-09-01',
                'end_date' => '2026-09-07',
                'status' => '0',
                'items' => [
                    ['product_id' => $product->id, 'discount' => 20, 'discount_type' => 'percent'],
                ],
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('flash-deals.index'));

        $flashDeal->refresh();

        $this->assertSame('New title', $flashDeal->title);
        $this->assertSame('new-title', $flashDeal->slug);
        $this->assertFalse($flashDeal->status);
        $this->assertCount(1, $flashDeal->items);
    }

    public function test_flash_deal_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $flashDeal = FlashDeal::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('flash-deals.destroy', $flashDeal));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('flash-deals.index'));

        $this->assertModelMissing($flashDeal);
    }

    public function test_guest_cannot_access_flash_deals(): void
    {
        $response = $this->get(route('flash-deals.index'));

        $response->assertRedirect(route('login'));
    }
}
