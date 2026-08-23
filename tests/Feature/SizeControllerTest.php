<?php

namespace Tests\Feature;

use App\Models\Size;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SizeControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_sizes_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $size = Size::factory()->create();

        $response = $this->actingAs($user)->get(route('sizes.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('sizes/Index')
                ->has('sizes.data', 1)
                ->where('sizes.data.0.id', $size->id),
            );
    }

    public function test_guests_cannot_view_sizes(): void
    {
        $response = $this->get(route('sizes.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_size_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('sizes.store'), [
            'name' => 'Small',
            'code' => 'S',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('sizes.index'));

        $this->assertDatabaseHas('sizes', [
            'name' => 'Small',
            'code' => 'S',
        ]);
    }

    public function test_size_name_must_be_unique_when_created(): void
    {
        $user = User::factory()->create();
        Size::factory()->create(['name' => 'Small']);

        $response = $this->actingAs($user)->post(route('sizes.store'), [
            'name' => 'Small',
            'code' => 'SM',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_size_code_must_be_unique_when_created(): void
    {
        $user = User::factory()->create();
        Size::factory()->create(['code' => 'S']);

        $response = $this->actingAs($user)->post(route('sizes.store'), [
            'name' => 'Slim',
            'code' => 'S',
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_size_can_be_updated(): void
    {
        $user = User::factory()->create();
        $size = Size::factory()->create();

        $response = $this->actingAs($user)->put(route('sizes.update', $size), [
            'name' => 'Updated name',
            'code' => 'UPD',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('sizes.index'));

        $size->refresh();

        $this->assertSame('Updated name', $size->name);
        $this->assertSame('UPD', $size->code);
    }

    public function test_size_name_must_be_unique_when_updated(): void
    {
        $user = User::factory()->create();
        Size::factory()->create(['name' => 'Small']);
        $size = Size::factory()->create(['name' => 'Large']);

        $response = $this->actingAs($user)->put(route('sizes.update', $size), [
            'name' => 'Small',
            'code' => $size->code,
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_size_can_keep_its_own_name_when_updated(): void
    {
        $user = User::factory()->create();
        $size = Size::factory()->create(['name' => 'Small', 'code' => 'S']);

        $response = $this->actingAs($user)->put(route('sizes.update', $size), [
            'name' => 'Small',
            'code' => 'S',
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_size_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $size = Size::factory()->create();

        $response = $this->actingAs($user)->delete(route('sizes.destroy', $size));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('sizes.index'));

        $this->assertModelMissing($size);
    }
}
