<?php

namespace Tests\Feature\Api\V1;

use App\Models\BlogCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogCategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_blog_categories_are_listed(): void
    {
        BlogCategory::factory()->count(3)->create(['status' => true]);
        BlogCategory::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.blog-categories.index'));

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'name', 'slug']]]);
    }

    public function test_blog_category_list_is_paginated(): void
    {
        BlogCategory::factory()->count(16)->create(['status' => true]);

        $response = $this->getJson(route('api.v1.blog-categories.index'));

        $response->assertOk()->assertJsonCount(15, 'data');
    }

    public function test_active_blog_category_can_be_shown(): void
    {
        $blogCategory = BlogCategory::factory()->create(['status' => true]);

        $response = $this->getJson(route('api.v1.blog-categories.show', $blogCategory));

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $blogCategory->id)
            ->assertJsonPath('data.name', $blogCategory->name);
    }

    public function test_inactive_blog_category_is_not_found(): void
    {
        $blogCategory = BlogCategory::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.blog-categories.show', $blogCategory));

        $response->assertNotFound();
    }

    public function test_missing_blog_category_is_not_found(): void
    {
        $response = $this->getJson(route('api.v1.blog-categories.show', 999999));

        $response->assertNotFound();
    }
}
