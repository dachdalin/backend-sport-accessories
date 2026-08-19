<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Services\CategorySlugService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CategorySlugServiceTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_it_slugifies_the_name(): void
    {
        $service = new CategorySlugService;

        $this->assertSame('running-shoes', $service->generate('Running Shoes'));
    }

    public function test_it_appends_a_suffix_when_the_slug_is_taken(): void
    {
        Category::factory()->create(['slug' => 'running-shoes']);

        $service = new CategorySlugService;

        $this->assertSame('running-shoes-1', $service->generate('Running Shoes'));
    }

    public function test_it_increments_the_suffix_until_unique(): void
    {
        Category::factory()->create(['slug' => 'running-shoes']);
        Category::factory()->create(['slug' => 'running-shoes-1']);

        $service = new CategorySlugService;

        $this->assertSame('running-shoes-2', $service->generate('Running Shoes'));
    }

    public function test_it_ignores_the_given_category_id(): void
    {
        $category = Category::factory()->create(['slug' => 'running-shoes']);

        $service = new CategorySlugService;

        $this->assertSame('running-shoes', $service->generate('Running Shoes', $category->id));
    }
}
