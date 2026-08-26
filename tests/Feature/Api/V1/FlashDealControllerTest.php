<?php

namespace Tests\Feature\Api\V1;

use App\Models\FlashDeal;
use App\Models\FlashDealProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashDealControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_running_flash_deals_are_listed(): void
    {
        FlashDeal::factory()->count(2)->create([
            'status' => true,
            'start_date' => today()->subDay(),
            'end_date' => today()->addDay(),
        ]);
        FlashDeal::factory()->create(['status' => false]);
        FlashDeal::factory()->create(['start_date' => today()->addWeek(), 'end_date' => today()->addWeeks(2)]);
        FlashDeal::factory()->create(['start_date' => today()->subWeeks(2), 'end_date' => today()->subWeek()]);

        $response = $this->getJson(route('api.v1.flash-deals.index'));

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure(['data' => [['id', 'title', 'slug', 'banner_url']]]);
    }

    public function test_running_flash_deal_can_be_shown_with_its_products(): void
    {
        $flashDeal = FlashDeal::factory()->create([
            'status' => true,
            'start_date' => today()->subDay(),
            'end_date' => today()->addDay(),
        ]);
        FlashDealProduct::factory()->count(2)->create(['flash_deal_id' => $flashDeal->id]);

        $response = $this->getJson(route('api.v1.flash-deals.show', $flashDeal));

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $flashDeal->id)
            ->assertJsonCount(2, 'data.products')
            ->assertJsonStructure(['data' => ['products' => [['id', 'discount', 'discount_type', 'product']]]]);
    }

    public function test_inactive_flash_deal_is_not_found(): void
    {
        $flashDeal = FlashDeal::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.flash-deals.show', $flashDeal));

        $response->assertNotFound();
    }

    public function test_expired_flash_deal_is_not_found(): void
    {
        $flashDeal = FlashDeal::factory()->create([
            'status' => true,
            'start_date' => today()->subWeeks(2),
            'end_date' => today()->subWeek(),
        ]);

        $response = $this->getJson(route('api.v1.flash-deals.show', $flashDeal));

        $response->assertNotFound();
    }

    public function test_missing_flash_deal_is_not_found(): void
    {
        $response = $this->getJson(route('api.v1.flash-deals.show', 999999));

        $response->assertNotFound();
    }
}
