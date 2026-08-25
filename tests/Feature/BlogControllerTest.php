<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BlogControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_blogs_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Blog::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('blogs.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('blogs/Index')
                ->has('blogs.data', 3),
            );
    }

    public function test_blogs_index_page_is_paginated(): void
    {
        $user = User::factory()->create();
        Blog::factory()->count(16)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('blogs.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('blogs/Index')
                ->has('blogs.data', 15),
            );
    }

    public function test_blog_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('blogs.create'));

        $response->assertOk();
    }

    public function test_blog_can_be_created(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $category = BlogCategory::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('blogs.store'), [
                'blog_category_id' => $category->id,
                'title' => 'Choosing the right running shoes',
                'writer' => 'Jane Doe',
                'description' => 'A guide to picking the right pair.',
                'image' => UploadedFile::fake()->image('post.jpg'),
                'is_published' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('blogs.index'));

        $blog = Blog::sole();

        $this->assertSame($category->id, $blog->blog_category_id);
        $this->assertSame('Choosing the right running shoes', $blog->title);
        $this->assertSame('choosing-the-right-running-shoes', $blog->slug);
        $this->assertTrue($blog->is_published);
        Storage::disk('public')->assertExists($blog->image);
    }

    public function test_blog_title_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('blogs.store'), [
                'description' => 'Missing a title.',
            ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_blog_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $blog = Blog::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('blogs.edit', $blog));

        $response->assertOk();
    }

    public function test_blog_can_be_updated(): void
    {
        $user = User::factory()->create();
        $blog = Blog::factory()->create(['title' => 'Old title']);

        $response = $this
            ->actingAs($user)
            ->put(route('blogs.update', $blog), [
                'blog_category_id' => $blog->blog_category_id,
                'title' => 'Updated title',
                'description' => $blog->description,
                'is_published' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('blogs.index'));

        $blog->refresh();

        $this->assertSame('Updated title', $blog->title);
        $this->assertSame('updated-title', $blog->slug);
        $this->assertTrue($blog->is_published);
    }

    public function test_blog_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $blog = Blog::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('blogs.destroy', $blog));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('blogs.index'));

        $this->assertModelMissing($blog);
    }

    public function test_guest_cannot_access_blogs(): void
    {
        $response = $this->get(route('blogs.index'));

        $response->assertRedirect(route('login'));
    }
}
