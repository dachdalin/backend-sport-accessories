<?php

namespace Tests\Feature;

use App\Models\Color;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ColorControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function actingAsUserWithPermission(string $permission): User
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::findOrCreate($permission));

        $this->actingAs($user);

        return $user;
    }

    /**
     * UserFactory assigns the admin role by default (so existing bare-user tests keep passing),
     * which bypasses every permission check via the Gate::before admin override. Access-denial
     * tests need a genuinely role-less user, so strip roles here instead of using the factory directly.
     */
    private function actingAsUserWithoutPermissions(): User
    {
        $user = User::factory()->create();
        $user->syncRoles([]);

        $this->actingAs($user);

        return $user;
    }

    public function test_colors_index_page_is_displayed(): void
    {
        $this->actingAsUserWithPermission('view colors');
        $color = Color::factory()->create();

        $response = $this->get(route('colors.index'));

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

    public function test_users_without_the_view_permission_cannot_view_colors(): void
    {
        $this->actingAsUserWithoutPermissions();

        $response = $this->get(route('colors.index'));

        $response->assertForbidden();
    }

    public function test_color_can_be_created(): void
    {
        $this->actingAsUserWithPermission('create colors');

        $response = $this->post(route('colors.store'), [
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

    public function test_users_without_the_create_permission_cannot_create_a_color(): void
    {
        $this->actingAsUserWithoutPermissions();

        $response = $this->post(route('colors.store'), [
            'name' => 'Red',
            'code' => '#ff0000',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('colors', ['name' => 'Red']);
    }

    public function test_color_name_must_be_unique_when_created(): void
    {
        $this->actingAsUserWithPermission('create colors');
        Color::factory()->create(['name' => 'Red']);

        $response = $this->post(route('colors.store'), [
            'name' => 'Red',
            'code' => '#ff0000',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_color_can_be_updated(): void
    {
        $this->actingAsUserWithPermission('edit colors');
        $color = Color::factory()->create();

        $response = $this->put(route('colors.update', $color), [
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

    public function test_users_without_the_edit_permission_cannot_update_a_color(): void
    {
        $this->actingAsUserWithoutPermissions();
        $color = Color::factory()->create(['name' => 'Red']);

        $response = $this->put(route('colors.update', $color), [
            'name' => 'Updated name',
            'code' => '#00ff00',
        ]);

        $response->assertForbidden();
        $this->assertSame('Red', $color->fresh()->name);
    }

    public function test_color_name_must_be_unique_when_updated(): void
    {
        $this->actingAsUserWithPermission('edit colors');
        Color::factory()->create(['name' => 'Red']);
        $color = Color::factory()->create(['name' => 'Blue']);

        $response = $this->put(route('colors.update', $color), [
            'name' => 'Red',
            'code' => $color->code,
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_color_can_keep_its_own_name_when_updated(): void
    {
        $this->actingAsUserWithPermission('edit colors');
        $color = Color::factory()->create(['name' => 'Red']);

        $response = $this->put(route('colors.update', $color), [
            'name' => 'Red',
            'code' => '#123456',
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_color_can_be_deleted(): void
    {
        $this->actingAsUserWithPermission('delete colors');
        $color = Color::factory()->create();

        $response = $this->delete(route('colors.destroy', $color));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('colors.index'));

        $this->assertModelMissing($color);
    }

    public function test_users_without_the_delete_permission_cannot_delete_a_color(): void
    {
        $this->actingAsUserWithoutPermissions();
        $color = Color::factory()->create();

        $response = $this->delete(route('colors.destroy', $color));

        $response->assertForbidden();
        $this->assertModelExists($color);
    }
}
