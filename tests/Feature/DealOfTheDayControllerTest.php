<?php

namespace Tests\Feature;

use App\Models\DealOfTheDay;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DealOfTheDayControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_deal_of_the_days_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        DealOfTheDay::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('deal-of-the-days.index'));

        $response->assertOk();
    }

    public function test_deal_of_the_day_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('deal-of-the-days.create'));

        $response->assertOk();
    }

    public function test_deal_of_the_day_can_be_created(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('deal-of-the-days.store'), [
                'title' => 'Weekend Flash Offer',
                'product_id' => $product->id,
                'discount' => '15.00',
                'discount_type' => 'percent',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('deal-of-the-days.index'));

        $deal = DealOfTheDay::sole();

        $this->assertSame('Weekend Flash Offer', $deal->title);
        $this->assertSame($product->id, $deal->product_id);
        $this->assertSame('15.00', $deal->discount);
        $this->assertTrue($deal->status);
    }

    public function test_deal_of_the_day_title_is_required(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('deal-of-the-days.store'), [
                'title' => '',
                'product_id' => $product->id,
            ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_deal_of_the_day_product_must_exist(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('deal-of-the-days.store'), [
                'title' => 'Weekend Flash Offer',
                'product_id' => 999999,
            ]);

        $response->assertSessionHasErrors('product_id');
    }

    public function test_deal_of_the_day_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $deal = DealOfTheDay::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('deal-of-the-days.edit', $deal));

        $response->assertOk();
    }

    public function test_deal_of_the_day_can_be_updated(): void
    {
        $user = User::factory()->create();
        $deal = DealOfTheDay::factory()->create(['status' => false]);
        $product = Product::factory()->create();

        $response = $this
            ->actingAs($user)
            ->put(route('deal-of-the-days.update', $deal), [
                'title' => 'Updated Offer',
                'product_id' => $product->id,
                'discount' => '20.00',
                'discount_type' => 'amount',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('deal-of-the-days.index'));

        $deal->refresh();

        $this->assertSame('Updated Offer', $deal->title);
        $this->assertSame($product->id, $deal->product_id);
        $this->assertTrue($deal->status);
    }

    public function test_deal_of_the_day_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $deal = DealOfTheDay::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('deal-of-the-days.destroy', $deal));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('deal-of-the-days.index'));

        $this->assertModelMissing($deal);
    }

    public function test_guest_cannot_access_deal_of_the_days(): void
    {
        $response = $this->get(route('deal-of-the-days.index'));

        $response->assertRedirect(route('login'));
    }
}
