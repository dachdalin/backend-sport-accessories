<?php

namespace Tests\Feature;

use App\Models\Color;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ColorControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_colors_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $color = Color::factory()->create();

        $response = $this->actingAs($user)->get(route('colors.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('colors/Index')
                ->has('colors.data', 1)
                ->where('colors.data.0.id', $color->id),
            );
    }

    public function test_guests_cannot_view_colors(): void
    {
        $response = $this->get(route('colors.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_color_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('colors.store'), [
            'name' => 'Red',
            'code' => '#ff0000',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('colors.index'));

        $this->assertDatabaseHas('colors', [
            'name' => 'Red',
            'code' => '#ff0000',
        ]);
    }

    public function test_color_name_must_be_unique_when_created(): void
    {
        $user = User::factory()->create();
        Color::factory()->create(['name' => 'Red']);

        $response = $this->actingAs($user)->post(route('colors.store'), [
            'name' => 'Red',
            'code' => '#ff0000',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_color_can_be_updated(): void
    {
        $user = User::factory()->create();
        $color = Color::factory()->create();

        $response = $this->actingAs($user)->put(route('colors.update', $color), [
            'name' => 'Updated name',
            'code' => '#00ff00',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('colors.index'));

        $color->refresh();

        $this->assertSame('Updated name', $color->name);
        $this->assertSame('#00ff00', $color->code);
    }

    public function test_color_name_must_be_unique_when_updated(): void
    {
        $user = User::factory()->create();
        Color::factory()->create(['name' => 'Red']);
        $color = Color::factory()->create(['name' => 'Blue']);

        $response = $this->actingAs($user)->put(route('colors.update', $color), [
            'name' => 'Red',
            'code' => $color->code,
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_color_can_keep_its_own_name_when_updated(): void
    {
        $user = User::factory()->create();
        $color = Color::factory()->create(['name' => 'Red']);

        $response = $this->actingAs($user)->put(route('colors.update', $color), [
            'name' => 'Red',
            'code' => '#123456',
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_color_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $color = Color::factory()->create();

        $response = $this->actingAs($user)->delete(route('colors.destroy', $color));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('colors.index'));

        $this->assertModelMissing($color);
    }
}
