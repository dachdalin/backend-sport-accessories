<?php

namespace Tests\Unit;

use App\Models\FlashDeal;
use App\Services\FlashDealSlugService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashDealSlugServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_slugifies_the_title(): void
    {
        $service = new FlashDealSlugService;

        $this->assertSame('weekend-sale', $service->generate('Weekend Sale'));
    }

    public function test_it_appends_a_suffix_when_the_slug_is_taken(): void
    {
        FlashDeal::factory()->create(['slug' => 'weekend-sale']);

        $service = new FlashDealSlugService;

        $this->assertSame('weekend-sale-1', $service->generate('Weekend Sale'));
    }

    public function test_it_increments_the_suffix_until_unique(): void
    {
        FlashDeal::factory()->create(['slug' => 'weekend-sale']);
        FlashDeal::factory()->create(['slug' => 'weekend-sale-1']);

        $service = new FlashDealSlugService;

        $this->assertSame('weekend-sale-2', $service->generate('Weekend Sale'));
    }

    public function test_it_ignores_the_given_flash_deal_id(): void
    {
        $flashDeal = FlashDeal::factory()->create(['slug' => 'weekend-sale']);

        $service = new FlashDealSlugService;

        $this->assertSame('weekend-sale', $service->generate('Weekend Sale', $flashDeal->id));
    }
}
