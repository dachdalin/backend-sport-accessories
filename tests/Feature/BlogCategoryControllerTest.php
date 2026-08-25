<?php

namespace Tests\Feature;

use App\Models\BlogCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BlogCategoryControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_blog_categories_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $blogCategory = BlogCategory::factory()->create();

        $response = $this->actingAs($user)->get(route('blog-categories.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('blog-categories/Index')
                ->has('blogCategories.data', 1)
                ->where('blogCategories.data.0.id', $blogCategory->id),
            );
    }

    public function test_blog_categories_index_page_is_paginated(): void
    {
        $user = User::factory()->create();
        BlogCategory::factory()->count(16)->create();

        $response = $this->actingAs($user)->get(route('blog-categories.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('blog-categories/Index')
                ->has('blogCategories.data', 15),
            );
    }

    public function test_guests_cannot_view_blog_categories(): void
    {
        $response = $this->get(route('blog-categories.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_blog_category_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('blog-categories.store'), [
            'name' => 'Training Tips',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('blog-categories.index'));

        $blogCategory = BlogCategory::sole();

        $this->assertSame('Training Tips', $blogCategory->name);
        $this->assertSame('training-tips', $blogCategory->slug);
        $this->assertTrue($blogCategory->status);
        $this->assertSame(0, $blogCategory->click_count);
    }

    public function test_blog_category_slug_is_made_unique_when_names_collide(): void
    {
        $user = User::factory()->create();
        BlogCategory::factory()->create(['name' => 'Training Tips', 'slug' => 'training-tips']);

        $response = $this->actingAs($user)->post(route('blog-categories.store'), [
            'name' => 'Training Tips!',
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertSame('training-tips-1', BlogCategory::latest('id')->first()->slug);
    }

    public function test_blog_category_name_must_be_unique(): void
    {
        $user = User::factory()->create();
        BlogCategory::factory()->create(['name' => 'Training Tips']);

        $response = $this->actingAs($user)->post(route('blog-categories.store'), [
            'name' => 'Training Tips',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_blog_category_can_be_created_as_inactive(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('blog-categories.store'), [
            'name' => 'Nutrition',
            'status' => '0',
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertFalse(BlogCategory::sole()->status);
    }

    public function test_blog_category_can_be_updated(): void
    {
        $user = User::factory()->create();
        $blogCategory = BlogCategory::factory()->create(['name' => 'Old name']);

        $response = $this->actingAs($user)->put(route('blog-categories.update', $blogCategory), [
            'name' => 'New name',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('blog-categories.index'));

        $blogCategory->refresh();

        $this->assertSame('New name', $blogCategory->name);
        $this->assertSame('new-name', $blogCategory->slug);
    }

    public function test_blog_category_keeps_its_slug_when_name_is_unchanged(): void
    {
        $user = User::factory()->create();
        $blogCategory = BlogCategory::factory()->create(['name' => 'Same name', 'slug' => 'same-name']);

        $response = $this->actingAs($user)->put(route('blog-categories.update', $blogCategory), [
            'name' => 'Same name',
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertSame('same-name', $blogCategory->refresh()->slug);
    }

    public function test_blog_category_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $blogCategory = BlogCategory::factory()->create();

        $response = $this->actingAs($user)->delete(route('blog-categories.destroy', $blogCategory));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('blog-categories.index'));

        $this->assertModelMissing($blogCategory);
    }
}
