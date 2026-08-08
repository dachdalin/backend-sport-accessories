<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
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
}
