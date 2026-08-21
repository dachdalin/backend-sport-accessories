<?php

namespace Tests\Feature;

use App\Models\Attribute;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AttributeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_attributes_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $attribute = Attribute::factory()->create();

        $response = $this->actingAs($user)->get(route('attributes.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('attributes/Index')
                ->has('attributes', 1)
                ->where('attributes.0.id', $attribute->id),
            );
    }

    public function test_guests_cannot_view_attributes(): void
    {
        $response = $this->get(route('attributes.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_attribute_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('attributes.store'), [
            'name' => 'Fabric',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('attributes.index'));

        $this->assertDatabaseHas('attributes', [
            'name' => 'Fabric',
        ]);
    }

    public function test_attribute_name_must_be_unique_when_created(): void
    {
        $user = User::factory()->create();
        Attribute::factory()->create(['name' => 'Fabric']);

        $response = $this->actingAs($user)->post(route('attributes.store'), [
            'name' => 'Fabric',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_attribute_can_be_updated(): void
    {
        $user = User::factory()->create();
        $attribute = Attribute::factory()->create();

        $response = $this->actingAs($user)->put(route('attributes.update', $attribute), [
            'name' => 'Updated name',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('attributes.index'));

        $attribute->refresh();

        $this->assertSame('Updated name', $attribute->name);
    }

    public function test_attribute_name_must_be_unique_when_updated(): void
    {
        $user = User::factory()->create();
        Attribute::factory()->create(['name' => 'Fabric']);
        $attribute = Attribute::factory()->create(['name' => 'Fit']);

        $response = $this->actingAs($user)->put(route('attributes.update', $attribute), [
            'name' => 'Fabric',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_attribute_can_keep_its_own_name_when_updated(): void
    {
        $user = User::factory()->create();
        $attribute = Attribute::factory()->create(['name' => 'Fabric']);

        $response = $this->actingAs($user)->put(route('attributes.update', $attribute), [
            'name' => 'Fabric',
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_attribute_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $attribute = Attribute::factory()->create();

        $response = $this->actingAs($user)->delete(route('attributes.destroy', $attribute));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('attributes.index'));

        $this->assertModelMissing($attribute);
    }
}
