<?php

namespace Tests\Feature\Api\V1;

use App\Models\Blog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_blogs_are_listed(): void
    {
        Blog::factory()->count(3)->create(['is_published' => true, 'published_at' => now()->subDay()]);
        Blog::factory()->create(['is_published' => false, 'published_at' => now()->subDay()]);
        Blog::factory()->create(['is_published' => true, 'published_at' => now()->addDay()]);

        $response = $this->getJson(route('api.v1.blogs.index'));

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'title', 'slug', 'writer', 'description', 'image_url', 'image_alt_text', 'published_at']]]);
    }

    public function test_blog_list_is_paginated(): void
    {
        Blog::factory()->count(16)->create(['is_published' => true, 'published_at' => now()->subDay()]);

        $response = $this->getJson(route('api.v1.blogs.index'));

        $response->assertOk()->assertJsonCount(15, 'data');
    }

    public function test_published_blog_can_be_shown(): void
    {
        $blog = Blog::factory()->create(['is_published' => true, 'published_at' => now()->subDay()]);

        $response = $this->getJson(route('api.v1.blogs.show', $blog));

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $blog->id)
            ->assertJsonPath('data.title', $blog->title);
    }

    public function test_unpublished_blog_is_not_found(): void
    {
        $blog = Blog::factory()->create(['is_published' => false, 'published_at' => now()->subDay()]);

        $response = $this->getJson(route('api.v1.blogs.show', $blog));

        $response->assertNotFound();
    }

    public function test_scheduled_blog_is_not_found(): void
    {
        $blog = Blog::factory()->create(['is_published' => true, 'published_at' => now()->addDay()]);

        $response = $this->getJson(route('api.v1.blogs.show', $blog));

        $response->assertNotFound();
    }

    public function test_missing_blog_is_not_found(): void
    {
        $response = $this->getJson(route('api.v1.blogs.show', 999999));

        $response->assertNotFound();
    }
}
