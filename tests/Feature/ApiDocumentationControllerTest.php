<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ApiDocumentationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_documentation_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::findOrCreate('view api documentation'));

        $response = $this
            ->actingAs($user)
            ->get(route('api-documentation.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('api-documentation/Index')
                ->has('groups')
                ->where('groups.0.endpoints.0.uri', fn (string $uri) => str_starts_with($uri, '/api/v1')),
            );
    }

    public function test_users_without_the_view_permission_cannot_view_api_documentation(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('api-documentation.index'));

        $response->assertForbidden();
    }

    public function test_guest_cannot_access_api_documentation(): void
    {
        $response = $this->get(route('api-documentation.index'));

        $response->assertRedirect(route('login'));
    }
}
