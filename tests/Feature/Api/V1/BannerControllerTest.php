<?php

namespace Tests\Feature\Api\V1;

use App\Models\Banner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BannerControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_banners_are_listed(): void
    {
        Banner::factory()->count(3)->create(['status' => true]);
        Banner::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.banners.index'));

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'title', 'image_url', 'image_alt_text', 'link_url', 'sort_order']]]);
    }

    public function test_banners_are_ordered_by_sort_order(): void
    {
        $last = Banner::factory()->create(['status' => true, 'sort_order' => 2]);
        $first = Banner::factory()->create(['status' => true, 'sort_order' => 0]);
        $middle = Banner::factory()->create(['status' => true, 'sort_order' => 1]);

        $response = $this->getJson(route('api.v1.banners.index'));

        $response
            ->assertOk()
            ->assertJsonPath('data.0.id', $first->id)
            ->assertJsonPath('data.1.id', $middle->id)
            ->assertJsonPath('data.2.id', $last->id);
    }

    public function test_banner_list_is_paginated(): void
    {
        Banner::factory()->count(16)->create(['status' => true]);

        $response = $this->getJson(route('api.v1.banners.index'));

        $response->assertOk()->assertJsonCount(15, 'data');
    }

    public function test_active_banner_can_be_shown(): void
    {
        $banner = Banner::factory()->create(['status' => true]);

        $response = $this->getJson(route('api.v1.banners.show', $banner));

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $banner->id)
            ->assertJsonPath('data.title', $banner->title);
    }

    public function test_inactive_banner_is_not_found(): void
    {
        $banner = Banner::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.banners.show', $banner));

        $response->assertNotFound();
    }

    public function test_missing_banner_is_not_found(): void
    {
        $response = $this->getJson(route('api.v1.banners.show', 999999));

        $response->assertNotFound();
    }
}
