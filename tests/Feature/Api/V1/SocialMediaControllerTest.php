<?php

namespace Tests\Feature\Api\V1;

use App\Models\SocialMedia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialMediaControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_social_medias_are_listed(): void
    {
        SocialMedia::factory()->count(3)->create(['status' => true]);
        SocialMedia::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.social-medias.index'));

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'name', 'link', 'icon']]]);
    }

    public function test_social_media_list_is_paginated(): void
    {
        SocialMedia::factory()->count(16)->create(['status' => true]);

        $response = $this->getJson(route('api.v1.social-medias.index'));

        $response->assertOk()->assertJsonCount(15, 'data');
    }

    public function test_active_social_media_can_be_shown(): void
    {
        $socialMedia = SocialMedia::factory()->create(['status' => true]);

        $response = $this->getJson(route('api.v1.social-medias.show', $socialMedia));

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $socialMedia->id)
            ->assertJsonPath('data.name', $socialMedia->name);
    }

    public function test_inactive_social_media_is_not_found(): void
    {
        $socialMedia = SocialMedia::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.social-medias.show', $socialMedia));

        $response->assertNotFound();
    }

    public function test_missing_social_media_is_not_found(): void
    {
        $response = $this->getJson(route('api.v1.social-medias.show', 999999));

        $response->assertNotFound();
    }
}
