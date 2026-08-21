<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StockClearanceSetup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockClearanceSetupControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_clearance_setups_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        StockClearanceSetup::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('stock-clearance-setups.index'));

        $response->assertOk();
    }

    public function test_stock_clearance_setup_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('stock-clearance-setups.create'));

        $response->assertOk();
    }

    public function test_stock_clearance_setup_can_be_created(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('stock-clearance-setups.store'), [
                'discount_type' => 'percent',
                'discount_amount' => '20.00',
                'offer_active_time' => 'always',
                'duration_start_date' => now()->toDateString(),
                'duration_end_date' => now()->addWeek()->toDateString(),
                'is_active' => true,
                'items' => [
                    ['product_id' => $product->id, 'discount_type' => 'percent', 'discount_amount' => '15.00'],
                ],
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('stock-clearance-setups.index'));

        $setup = StockClearanceSetup::sole();

        $this->assertSame('20.00', $setup->discount_amount);
        $this->assertCount(1, $setup->items);
        $this->assertSame($product->id, $setup->items->first()->product_id);
    }

    public function test_stock_clearance_setup_requires_at_least_one_item(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('stock-clearance-setups.store'), [
                'discount_type' => 'percent',
                'discount_amount' => '20.00',
                'offer_active_time' => 'always',
                'duration_start_date' => now()->toDateString(),
                'duration_end_date' => now()->addWeek()->toDateString(),
                'items' => [],
            ]);

        $response->assertSessionHasErrors('items');
    }

    public function test_stock_clearance_setup_specific_time_requires_active_range(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('stock-clearance-setups.store'), [
                'discount_type' => 'percent',
                'discount_amount' => '20.00',
                'offer_active_time' => 'specific_time',
                'duration_start_date' => now()->toDateString(),
                'duration_end_date' => now()->addWeek()->toDateString(),
                'items' => [
                    ['product_id' => $product->id, 'discount_type' => 'percent', 'discount_amount' => '15.00'],
                ],
            ]);

        $response->assertSessionHasErrors(['offer_active_range_start', 'offer_active_range_end']);
    }

    public function test_stock_clearance_setup_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $setup = StockClearanceSetup::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('stock-clearance-setups.edit', $setup));

        $response->assertOk();
    }

    public function test_stock_clearance_setup_can_be_updated(): void
    {
        $user = User::factory()->create();
        $setup = StockClearanceSetup::factory()->create(['discount_amount' => '10.00']);
        $product = Product::factory()->create();

        $response = $this
            ->actingAs($user)
            ->put(route('stock-clearance-setups.update', $setup), [
                'discount_type' => 'amount',
                'discount_amount' => '30.00',
                'offer_active_time' => 'always',
                'duration_start_date' => now()->toDateString(),
                'duration_end_date' => now()->addWeek()->toDateString(),
                'is_active' => false,
                'items' => [
                    ['product_id' => $product->id, 'discount_type' => 'amount', 'discount_amount' => '5.00'],
                ],
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('stock-clearance-setups.index'));

        $setup->refresh();

        $this->assertSame('30.00', $setup->discount_amount);
        $this->assertFalse($setup->is_active);
        $this->assertCount(1, $setup->items);
    }

    public function test_stock_clearance_setup_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $setup = StockClearanceSetup::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('stock-clearance-setups.destroy', $setup));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('stock-clearance-setups.index'));

        $this->assertModelMissing($setup);
    }

    public function test_guest_cannot_access_stock_clearance_setups(): void
    {
        $response = $this->get(route('stock-clearance-setups.index'));

        $response->assertRedirect(route('login'));
    }
}
