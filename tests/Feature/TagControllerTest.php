<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TagControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_tags_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create();

        $response = $this->actingAs($user)->get(route('tags.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('tags/Index')
                ->has('tags.data', 1)
                ->where('tags.data.0.id', $tag->id),
            );
    }

    public function test_guests_cannot_view_tags(): void
    {
        $response = $this->get(route('tags.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_tag_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tags.store'), [
            'tag' => 'Running Shoes',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('tags.index'));

        $this->assertDatabaseHas('tags', [
            'tag' => 'Running Shoes',
            'visit_count' => 0,
        ]);
    }

    public function test_tag_is_normalized_before_saving(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tags.store'), [
            'tag' => '  Running    Shoes  ',
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('tags', [
            'tag' => 'Running Shoes',
        ]);
    }

    public function test_visit_count_cannot_be_mass_assigned_on_create(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('tags.store'), [
            'tag' => 'Running Shoes',
            'visit_count' => 999,
        ]);

        $this->assertDatabaseHas('tags', [
            'tag' => 'Running Shoes',
            'visit_count' => 0,
        ]);
    }

    public function test_duplicate_tag_is_rejected_case_insensitively_when_created(): void
    {
        $user = User::factory()->create();
        Tag::factory()->create(['tag' => 'Running Shoes']);

        $response = $this->actingAs($user)->post(route('tags.store'), [
            'tag' => 'running shoes',
        ]);

        $response->assertSessionHasErrors('tag');
    }

    public function test_tag_can_be_updated(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create();

        $response = $this->actingAs($user)->put(route('tags.update', $tag), [
            'tag' => 'Updated tag',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('tags.index'));

        $this->assertSame('Updated tag', $tag->refresh()->tag);
    }

    public function test_tag_can_keep_its_own_value_when_updated(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['tag' => 'Running Shoes']);

        $response = $this->actingAs($user)->put(route('tags.update', $tag), [
            'tag' => 'Running Shoes',
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_duplicate_tag_is_rejected_case_insensitively_when_updated(): void
    {
        $user = User::factory()->create();
        Tag::factory()->create(['tag' => 'Running Shoes']);
        $tag = Tag::factory()->create(['tag' => 'Sandals']);

        $response = $this->actingAs($user)->put(route('tags.update', $tag), [
            'tag' => 'running shoes',
        ]);

        $response->assertSessionHasErrors('tag');
    }

    public function test_tag_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create();

        $response = $this->actingAs($user)->delete(route('tags.destroy', $tag));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('tags.index'));

        $this->assertModelMissing($tag);
    }
}
