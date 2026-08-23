<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_categories_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->get(route('categories.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('categories/Index')
                ->has('categories.data', 1)
                ->where('categories.data.0.id', $category->id),
            );
    }

    public function test_guests_cannot_view_categories(): void
    {
        $response = $this->get(route('categories.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_category_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('categories.store'), [
            'name' => 'Running Shoes',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('categories.index'));

        $category = Category::sole();

        $this->assertSame('Running Shoes', $category->name);
        $this->assertSame('running-shoes', $category->slug);
        $this->assertSame('def.png', $category->icon);
        $this->assertNull($category->parent_id);
        $this->assertFalse($category->home_status);
    }

    public function test_category_slug_is_made_unique_when_names_collide(): void
    {
        $user = User::factory()->create();
        Category::factory()->create(['name' => 'Running Shoes', 'slug' => 'running-shoes']);

        $response = $this->actingAs($user)->post(route('categories.store'), [
            'name' => 'Running Shoes',
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertSame('running-shoes-1', Category::latest('id')->first()->slug);
    }

    public function test_category_can_be_created_under_a_parent(): void
    {
        $user = User::factory()->create();
        $parent = Category::factory()->create();

        $response = $this->actingAs($user)->post(route('categories.store'), [
            'name' => 'Trail Shoes',
            'parent_id' => $parent->id,
            'home_status' => '1',
        ]);

        $response->assertSessionHasNoErrors();

        $category = Category::whereKeyNot($parent->id)->sole();

        $this->assertSame($parent->id, $category->parent_id);
        $this->assertTrue($category->home_status);
    }

    public function test_category_can_be_updated(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => 'Old name']);

        $response = $this->actingAs($user)->put(route('categories.update', $category), [
            'name' => 'New name',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('categories.index'));

        $category->refresh();

        $this->assertSame('New name', $category->name);
        $this->assertSame('new-name', $category->slug);
    }

    public function test_category_keeps_its_slug_when_name_is_unchanged(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => 'Same name', 'slug' => 'same-name']);

        $response = $this->actingAs($user)->put(route('categories.update', $category), [
            'name' => 'Same name',
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertSame('same-name', $category->refresh()->slug);
    }

    public function test_category_cannot_be_its_own_parent(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->put(route('categories.update', $category), [
            'name' => $category->name,
            'parent_id' => $category->id,
        ]);

        $response->assertSessionHasErrors('parent_id');
    }

    public function test_category_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->delete(route('categories.destroy', $category));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('categories.index'));

        $this->assertModelMissing($category);
    }

    public function test_deleting_a_parent_category_orphans_its_children(): void
    {
        $user = User::factory()->create();
        $parent = Category::factory()->create();
        $child = Category::factory()->childOf($parent)->create();

        $this->actingAs($user)->delete(route('categories.destroy', $parent));

        $this->assertNull($child->refresh()->parent_id);
    }
}
