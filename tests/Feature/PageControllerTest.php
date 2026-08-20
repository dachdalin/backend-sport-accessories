<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PageControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_pages_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Page::factory()->count(3)->create();

        $response = $this->actingAs($user)->get(route('pages.index'));

        $response->assertOk();
    }

    public function test_guests_cannot_view_pages(): void
    {
        $response = $this->get(route('pages.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_page_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('pages.create'));

        $response->assertOk();
    }

    public function test_page_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('pages.store'), [
            'title' => 'About Us',
            'content' => 'This is the about us page.',
            'status' => '1',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('pages.index'));

        $page = Page::sole();

        $this->assertSame('About Us', $page->title);
        $this->assertSame('about-us', $page->slug);
        $this->assertTrue($page->status);
    }

    public function test_page_slug_is_unique_when_titles_collide(): void
    {
        $user = User::factory()->create();
        Page::factory()->create(['title' => 'About Us', 'slug' => 'about-us']);

        $this->actingAs($user)->post(route('pages.store'), [
            'title' => 'About Us',
            'content' => 'Another about us page.',
        ]);

        $this->assertDatabaseHas('pages', ['slug' => 'about-us-1']);
    }

    public function test_page_title_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('pages.store'), [
            'title' => '',
            'content' => 'Some content',
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_page_content_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('pages.store'), [
            'title' => 'Terms',
            'content' => '',
        ]);

        $response->assertSessionHasErrors('content');
    }

    public function test_page_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $page = Page::factory()->create();

        $response = $this->actingAs($user)->get(route('pages.edit', $page));

        $response->assertOk();
    }

    public function test_page_can_be_updated(): void
    {
        $user = User::factory()->create();
        $page = Page::factory()->create(['title' => 'About Us', 'slug' => 'about-us', 'status' => true]);

        $response = $this->actingAs($user)->put(route('pages.update', $page), [
            'title' => 'Contact Us',
            'content' => 'Updated content.',
            'status' => '0',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('pages.index'));

        $page->refresh();

        $this->assertSame('Contact Us', $page->title);
        $this->assertSame('contact-us', $page->slug);
        $this->assertFalse($page->status);
    }

    public function test_page_slug_stays_the_same_when_title_is_unchanged(): void
    {
        $user = User::factory()->create();
        $page = Page::factory()->create(['title' => 'About Us', 'slug' => 'about-us']);

        $this->actingAs($user)->put(route('pages.update', $page), [
            'title' => 'About Us',
            'content' => 'Updated content only.',
        ]);

        $page->refresh();

        $this->assertSame('about-us', $page->slug);
    }

    public function test_page_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $page = Page::factory()->create();

        $response = $this->actingAs($user)->delete(route('pages.destroy', $page));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('pages.index'));

        $this->assertModelMissing($page);
    }
}
