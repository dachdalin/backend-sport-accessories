<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BannerControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_banners_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Banner::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('banners.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('banners/Index')
                ->has('banners.data', 3),
            );
    }

    public function test_banner_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('banners.create'));

        $response->assertOk();
    }

    public function test_banner_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('banners.store'), [
                'title' => 'Summer Sale',
                'image_alt_text' => 'Summer sale banner',
                'link_url' => 'https://example.com/summer-sale',
                'sort_order' => '5',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('banners.index'));

        $banner = Banner::sole();

        $this->assertSame('Summer Sale', $banner->title);
        $this->assertSame('def.png', $banner->image);
        $this->assertSame(5, $banner->sort_order);
        $this->assertTrue($banner->status);
    }

    public function test_banner_title_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('banners.store'), [
                'title' => '',
            ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_banner_link_url_must_be_a_valid_url(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('banners.store'), [
                'title' => 'Winter Sale',
                'link_url' => 'not-a-url',
            ]);

        $response->assertSessionHasErrors('link_url');
    }

    public function test_banner_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $banner = Banner::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('banners.edit', $banner));

        $response->assertOk();
    }

    public function test_banner_can_be_updated(): void
    {
        $user = User::factory()->create();
        $banner = Banner::factory()->create(['status' => true]);

        $response = $this
            ->actingAs($user)
            ->put(route('banners.update', $banner), [
                'title' => 'Winter Sale',
                'image_alt_text' => $banner->image_alt_text,
                'link_url' => $banner->link_url,
                'sort_order' => '10',
                'status' => '0',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('banners.index'));

        $banner->refresh();

        $this->assertSame('Winter Sale', $banner->title);
        $this->assertSame(10, $banner->sort_order);
        $this->assertFalse($banner->status);
    }

    public function test_banner_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $banner = Banner::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('banners.destroy', $banner));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('banners.index'));

        $this->assertModelMissing($banner);
    }
}
