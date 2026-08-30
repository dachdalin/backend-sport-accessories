<?php

namespace Tests\Feature;

use App\Models\SocialMedia;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SocialMediaControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_social_medias_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $socialMedia = SocialMedia::factory()->create();

        $response = $this->actingAs($user)->get(route('social-medias.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('social-medias/Index')
                ->has('socialMedias', 1)
                ->where('socialMedias.0.id', $socialMedia->id),
            );
    }

    public function test_guests_cannot_view_social_medias(): void
    {
        $response = $this->get(route('social-medias.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_social_media_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('social-medias.store'), [
            'name' => 'Facebook',
            'link' => 'https://facebook.com/example',
            'icon' => 'facebook',
            'status' => '1',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('social-medias.index'));

        $socialMedia = SocialMedia::sole();

        $this->assertSame('Facebook', $socialMedia->name);
        $this->assertSame('https://facebook.com/example', $socialMedia->link);
        $this->assertSame('facebook', $socialMedia->icon);
        $this->assertTrue($socialMedia->status);
    }

    public function test_social_media_link_must_be_a_valid_url(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('social-medias.store'), [
            'name' => 'Facebook',
            'link' => 'not-a-url',
        ]);

        $response->assertSessionHasErrors('link');
    }

    public function test_social_media_can_be_updated(): void
    {
        $user = User::factory()->create();
        $socialMedia = SocialMedia::factory()->create(['status' => true]);

        $response = $this->actingAs($user)->put(route('social-medias.update', $socialMedia), [
            'name' => 'Instagram',
            'link' => 'https://instagram.com/example',
            'icon' => 'instagram',
            'status' => '0',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('social-medias.index'));

        $socialMedia->refresh();

        $this->assertSame('Instagram', $socialMedia->name);
        $this->assertFalse($socialMedia->status);
    }

    public function test_social_media_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $socialMedia = SocialMedia::factory()->create();

        $response = $this->actingAs($user)->delete(route('social-medias.destroy', $socialMedia));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('social-medias.index'));

        $this->assertModelMissing($socialMedia);
    }
}
