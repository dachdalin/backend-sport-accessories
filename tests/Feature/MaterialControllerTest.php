<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class MaterialControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_materials_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $material = Material::factory()->create();

        $response = $this->actingAs($user)->get(route('materials.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('materials/Index')
                ->has('materials', 1)
                ->where('materials.0.id', $material->id),
            );
    }

    public function test_guests_cannot_view_materials(): void
    {
        $response = $this->get(route('materials.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_material_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('materials.store'), [
            'name' => 'Nylon',
            'code' => 'NYL',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('materials.index'));

        $this->assertDatabaseHas('materials', [
            'name' => 'Nylon',
            'code' => 'NYL',
        ]);
    }

    public function test_material_name_must_be_unique_when_created(): void
    {
        $user = User::factory()->create();
        Material::factory()->create(['name' => 'Nylon']);

        $response = $this->actingAs($user)->post(route('materials.store'), [
            'name' => 'Nylon',
            'code' => 'NYL2',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_material_code_must_be_unique_when_created(): void
    {
        $user = User::factory()->create();
        Material::factory()->create(['code' => 'NYL']);

        $response = $this->actingAs($user)->post(route('materials.store'), [
            'name' => 'Nylon Blend',
            'code' => 'NYL',
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_material_can_be_updated(): void
    {
        $user = User::factory()->create();
        $material = Material::factory()->create();

        $response = $this->actingAs($user)->put(route('materials.update', $material), [
            'name' => 'Updated name',
            'code' => 'UPD',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('materials.index'));

        $material->refresh();

        $this->assertSame('Updated name', $material->name);
        $this->assertSame('UPD', $material->code);
    }

    public function test_material_name_must_be_unique_when_updated(): void
    {
        $user = User::factory()->create();
        Material::factory()->create(['name' => 'Nylon']);
        $material = Material::factory()->create(['name' => 'Leather']);

        $response = $this->actingAs($user)->put(route('materials.update', $material), [
            'name' => 'Nylon',
            'code' => $material->code,
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_material_can_keep_its_own_name_when_updated(): void
    {
        $user = User::factory()->create();
        $material = Material::factory()->create(['name' => 'Nylon', 'code' => 'NYL']);

        $response = $this->actingAs($user)->put(route('materials.update', $material), [
            'name' => 'Nylon',
            'code' => 'NYL',
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_material_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $material = Material::factory()->create();

        $response = $this->actingAs($user)->delete(route('materials.destroy', $material));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('materials.index'));

        $this->assertModelMissing($material);
    }
}
