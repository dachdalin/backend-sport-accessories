<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_roles_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Editor']);

        $response = $this->actingAs($user)->get(route('roles.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('roles/Index')
                ->has('roles', 1)
                ->where('roles.0.id', $role->id),
            );
    }

    public function test_guests_cannot_view_roles(): void
    {
        $response = $this->get(route('roles.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_role_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('roles.store'), [
            'name' => 'Editor',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('roles.index'));

        $this->assertDatabaseHas('roles', [
            'name' => 'Editor',
            'guard_name' => 'web',
        ]);
    }

    public function test_role_can_be_created_with_permissions(): void
    {
        $user = User::factory()->create();
        $permission = Permission::create(['name' => 'products.view']);

        $response = $this->actingAs($user)->post(route('roles.store'), [
            'name' => 'Editor',
            'permissions' => [$permission->id],
        ]);

        $response->assertSessionHasNoErrors();

        $role = Role::where('name', 'Editor')->firstOrFail();

        $this->assertTrue($role->hasPermissionTo($permission));
    }

    public function test_duplicate_role_name_is_rejected(): void
    {
        $user = User::factory()->create();
        Role::create(['name' => 'Editor']);

        $response = $this->actingAs($user)->post(route('roles.store'), [
            'name' => 'Editor',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_unknown_permission_id_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('roles.store'), [
            'name' => 'Editor',
            'permissions' => [999],
        ]);

        $response->assertSessionHasErrors('permissions.0');
    }

    public function test_role_can_be_updated(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Editor']);
        $permission = Permission::create(['name' => 'products.view']);

        $response = $this->actingAs($user)->put(route('roles.update', $role), [
            'name' => 'Content editor',
            'permissions' => [$permission->id],
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('roles.index'));

        $role->refresh();

        $this->assertSame('Content editor', $role->name);
        $this->assertTrue($role->hasPermissionTo($permission));
    }

    public function test_updating_a_role_replaces_its_permissions(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Editor']);
        $keep = Permission::create(['name' => 'products.view']);
        $drop = Permission::create(['name' => 'products.delete']);
        $role->syncPermissions([$keep->id, $drop->id]);

        $response = $this->actingAs($user)->put(route('roles.update', $role), [
            'name' => 'Editor',
            'permissions' => [$keep->id],
        ]);

        $response->assertSessionHasNoErrors();

        $role->refresh();

        $this->assertTrue($role->hasPermissionTo($keep));
        $this->assertFalse($role->hasPermissionTo($drop));
    }

    public function test_duplicate_role_name_is_rejected_when_updated(): void
    {
        $user = User::factory()->create();
        Role::create(['name' => 'Editor']);
        $role = Role::create(['name' => 'Support']);

        $response = $this->actingAs($user)->put(route('roles.update', $role), [
            'name' => 'Editor',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_role_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Editor']);

        $response = $this->actingAs($user)->delete(route('roles.destroy', $role));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('roles.index'));

        $this->assertModelMissing($role);
    }

    public function test_admin_role_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'admin']);

        $response = $this->actingAs($user)->delete(route('roles.destroy', $role));

        $response->assertStatus(302);

        $this->assertModelExists($role);
    }
}
