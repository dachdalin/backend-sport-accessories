<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_role_can_be_assigned_to_a_user(): void
    {
        Role::findOrCreate('admin');
        $user = User::factory()->create();

        $user->assignRole('admin');

        $this->assertTrue($user->fresh()->hasRole('admin'));
    }

    public function test_role_middleware_denies_users_without_the_role(): void
    {
        Role::findOrCreate('admin');
        Route::middleware(['web', 'auth', 'role:admin'])->get('/admin-only', fn () => 'ok');

        $user = User::factory()->create();
        $user->syncRoles([]);
        $this->actingAs($user);

        $response = $this->get('/admin-only');

        $response->assertForbidden();
    }

    public function test_role_middleware_allows_users_with_the_role(): void
    {
        Role::findOrCreate('admin');
        Route::middleware(['web', 'auth', 'role:admin'])->get('/admin-only', fn () => 'ok');

        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);

        $response = $this->get('/admin-only');

        $response->assertOk();
    }

    public function test_role_permission_seeder_grants_admin_every_permission(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = Role::findByName('admin');

        $this->assertSame(Permission::count(), $admin->permissions()->count());
        $this->assertGreaterThan(0, $admin->permissions()->count());
    }

    public function test_role_permission_seeder_grants_manager_view_only_access(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $manager = Role::findByName('manager');
        $permissionNames = $manager->permissions()->pluck('name');

        $this->assertTrue($permissionNames->contains('view products'));
        $this->assertTrue($permissionNames->every(fn (string $name) => str_starts_with($name, 'view ')));
        $this->assertFalse($permissionNames->contains('create products'));
        $this->assertFalse($permissionNames->contains('view users'));
        $this->assertFalse($permissionNames->contains('view roles'));
        $this->assertFalse($permissionNames->contains('view business settings'));
    }

    public function test_admin_seeder_assigns_the_correct_role_to_each_demo_user(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $this->seed(AdminSeeder::class);

        $admin = User::where('email', 'admin@example.com')->firstOrFail();
        $manager = User::where('email', 'manager@example.com')->firstOrFail();
        $catalogManager = User::where('email', 'catalog-manager@example.com')->firstOrFail();
        $customerManager = User::where('email', 'customer-manager@example.com')->firstOrFail();
        $contentManager = User::where('email', 'content-manager@example.com')->firstOrFail();
        $support = User::where('email', 'support@example.com')->firstOrFail();

        $this->assertTrue($admin->hasRole('admin'));
        $this->assertTrue($manager->hasRole('manager'));
        $this->assertTrue($catalogManager->hasRole('catalog manager'));
        $this->assertTrue($customerManager->hasRole('customer manager'));
        $this->assertTrue($contentManager->hasRole('content manager'));
        $this->assertTrue($support->hasRole('support'));
        $this->assertFalse($manager->hasRole('admin'));
    }

    public function test_role_permission_seeder_grants_catalog_manager_full_control_over_catalog_and_sales(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $catalogManager = Role::findByName('catalog manager');
        $permissionNames = $catalogManager->permissions()->pluck('name');

        $this->assertTrue($permissionNames->contains('view products'));
        $this->assertTrue($permissionNames->contains('create products'));
        $this->assertTrue($permissionNames->contains('edit products'));
        $this->assertTrue($permissionNames->contains('delete products'));

        foreach (['attributes', 'colors', 'sizes', 'materials', 'brands', 'tags'] as $resource) {
            $this->assertTrue($permissionNames->contains("view {$resource}"));
            $this->assertTrue($permissionNames->contains("create {$resource}"));
            $this->assertTrue($permissionNames->contains("edit {$resource}"));
            $this->assertTrue($permissionNames->contains("delete {$resource}"));
        }

        $this->assertTrue($permissionNames->contains('view dashboard'));
        $this->assertFalse($permissionNames->contains('view users'));
        $this->assertFalse($permissionNames->contains('view roles'));
        $this->assertFalse($permissionNames->contains('view customers'));
    }

    public function test_role_permission_seeder_grants_customer_manager_full_control_over_customer_relationship_resources(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $customerManager = Role::findByName('customer manager');
        $permissionNames = $customerManager->permissions()->pluck('name');

        foreach (['customers', 'orders', 'wishlists', 'refund requests', 'reviews'] as $resource) {
            $this->assertTrue($permissionNames->contains("view {$resource}"));
            $this->assertTrue($permissionNames->contains("create {$resource}"));
            $this->assertTrue($permissionNames->contains("edit {$resource}"));
            $this->assertTrue($permissionNames->contains("delete {$resource}"));
        }

        $this->assertTrue($permissionNames->contains('view dashboard'));
        $this->assertFalse($permissionNames->contains('view users'));
        $this->assertFalse($permissionNames->contains('view roles'));
        $this->assertFalse($permissionNames->contains('view products'));
        $this->assertFalse($permissionNames->contains('view support tickets'));
    }

    public function test_role_permission_seeder_grants_content_manager_full_control_over_content_resources(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $contentManager = Role::findByName('content manager');
        $permissionNames = $contentManager->permissions()->pluck('name');

        foreach (['pages', 'blogs', 'blog categories'] as $resource) {
            $this->assertTrue($permissionNames->contains("view {$resource}"));
            $this->assertTrue($permissionNames->contains("create {$resource}"));
            $this->assertTrue($permissionNames->contains("edit {$resource}"));
            $this->assertTrue($permissionNames->contains("delete {$resource}"));
        }

        $this->assertTrue($permissionNames->contains('view dashboard'));
        $this->assertFalse($permissionNames->contains('view users'));
        $this->assertFalse($permissionNames->contains('view roles'));
        $this->assertFalse($permissionNames->contains('view customers'));
        $this->assertFalse($permissionNames->contains('view products'));
        $this->assertFalse($permissionNames->contains('view orders'));
    }

    public function test_role_permission_seeder_grants_support_ticket_and_contact_control_with_read_only_customer_context(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $support = Role::findByName('support');
        $permissionNames = $support->permissions()->pluck('name');

        $this->assertTrue($permissionNames->contains('view support tickets'));
        $this->assertTrue($permissionNames->contains('create support tickets'));
        $this->assertTrue($permissionNames->contains('edit support tickets'));
        $this->assertTrue($permissionNames->contains('delete support tickets'));
        $this->assertTrue($permissionNames->contains('view contacts'));
        $this->assertTrue($permissionNames->contains('view customers'));
        $this->assertTrue($permissionNames->contains('view orders'));
        $this->assertFalse($permissionNames->contains('create customers'));
        $this->assertFalse($permissionNames->contains('edit orders'));
    }
}
