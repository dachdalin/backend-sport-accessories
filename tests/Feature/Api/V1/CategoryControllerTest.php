<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_categories_are_listed(): void
    {
        Category::factory()->count(3)->create();

        $response = $this->getJson(route('api.v1.categories.index'));

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'name', 'slug', 'icon_url', 'parent_id']]]);
    }

    public function test_category_list_is_paginated(): void
    {
        Category::factory()->count(16)->create();

        $response = $this->getJson(route('api.v1.categories.index'));

        $response->assertOk()->assertJsonCount(15, 'data');
    }

    public function test_category_can_be_shown(): void
    {
        $category = Category::factory()->create();

        $response = $this->getJson(route('api.v1.categories.show', $category));

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $category->id)
            ->assertJsonPath('data.name', $category->name);
    }

    public function test_missing_category_is_not_found(): void
    {
        $response = $this->getJson(route('api.v1.categories.show', 999999));

        $response->assertNotFound();
    }
}
