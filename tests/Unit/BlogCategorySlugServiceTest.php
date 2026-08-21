<?php

namespace Tests\Unit;

use App\Models\BlogCategory;
use App\Services\BlogCategorySlugService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class BlogCategorySlugServiceTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_it_slugifies_the_name(): void
    {
        $service = new BlogCategorySlugService;

        $this->assertSame('training-tips', $service->generate('Training Tips'));
    }

    public function test_it_appends_a_suffix_when_the_slug_is_taken(): void
    {
        BlogCategory::factory()->create(['slug' => 'training-tips']);

        $service = new BlogCategorySlugService;

        $this->assertSame('training-tips-1', $service->generate('Training Tips'));
    }

    public function test_it_increments_the_suffix_until_unique(): void
    {
        BlogCategory::factory()->create(['slug' => 'training-tips']);
        BlogCategory::factory()->create(['slug' => 'training-tips-1']);

        $service = new BlogCategorySlugService;

        $this->assertSame('training-tips-2', $service->generate('Training Tips'));
    }

    public function test_it_ignores_the_given_blog_category_id(): void
    {
        $blogCategory = BlogCategory::factory()->create(['slug' => 'training-tips']);

        $service = new BlogCategorySlugService;

        $this->assertSame('training-tips', $service->generate('Training Tips', $blogCategory->id));
    }
}
