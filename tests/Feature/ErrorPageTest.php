<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ErrorPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_route_renders_branded_404_page(): void
    {
        $response = $this->get('/this-route-does-not-exist');

        $response->assertNotFound();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('ErrorPage')
            ->where('status', 404)
        );
    }

    public function test_forbidden_route_renders_branded_403_page(): void
    {
        $user = User::factory()->create();
        $user->syncRoles([]);
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));

        $response->assertForbidden();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('ErrorPage')
            ->where('status', 403)
        );
    }
}
