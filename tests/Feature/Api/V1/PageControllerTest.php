<?php

namespace Tests\Feature\Api\V1;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_pages_are_listed(): void
    {
        Page::factory()->count(3)->create(['status' => true]);
        Page::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.pages.index'));

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'title', 'slug', 'content', 'meta_title', 'meta_description']]]);
    }

    public function test_page_list_is_paginated(): void
    {
        Page::factory()->count(16)->create(['status' => true]);

        $response = $this->getJson(route('api.v1.pages.index'));

        $response->assertOk()->assertJsonCount(15, 'data');
    }

    public function test_active_page_can_be_shown(): void
    {
        $page = Page::factory()->create(['status' => true]);

        $response = $this->getJson(route('api.v1.pages.show', $page));

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $page->id)
            ->assertJsonPath('data.title', $page->title);
    }

    public function test_inactive_page_is_not_found(): void
    {
        $page = Page::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.pages.show', $page));

        $response->assertNotFound();
    }

    public function test_missing_page_is_not_found(): void
    {
        $response = $this->getJson(route('api.v1.pages.show', 999999));

        $response->assertNotFound();
    }
}
