<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register(): void
    {
        $response = $this->postJson(route('api.v1.auth.register'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response
            ->assertCreated()
            ->assertJsonStructure(['customer' => ['id', 'name', 'email'], 'token']);

        $this->assertDatabaseHas('customers', ['email' => 'jane@example.com']);
    }

    public function test_customer_registration_requires_unique_email(): void
    {
        Customer::factory()->create(['email' => 'jane@example.com']);

        $response = $this->postJson(route('api.v1.auth.register'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_customer_can_login(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->postJson(route('api.v1.auth.login'), [
            'email' => $customer->email,
            'password' => 'password',
            'device_name' => 'iphone-15',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure(['customer' => ['id', 'name', 'email'], 'token']);
    }

    public function test_customer_cannot_login_with_invalid_password(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->postJson(route('api.v1.auth.login'), [
            'email' => $customer->email,
            'password' => 'wrong-password',
            'device_name' => 'iphone-15',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_deactivated_customer_cannot_login(): void
    {
        $customer = Customer::factory()->create(['status' => false]);

        $response = $this->postJson(route('api.v1.auth.login'), [
            'email' => $customer->email,
            'password' => 'password',
            'device_name' => 'iphone-15',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_customer_can_logout(): void
    {
        $customer = Customer::factory()->create();
        Sanctum::actingAs($customer);

        $response = $this->postJson(route('api.v1.auth.logout'));

        $response->assertNoContent();
    }

    public function test_guest_cannot_logout(): void
    {
        $response = $this->postJson(route('api.v1.auth.logout'));

        $response->assertUnauthorized();
    }

    public function test_customer_auth_endpoints_are_rate_limited(): void
    {
        $customer = Customer::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->postJson(route('api.v1.auth.login'), [
                'email' => $customer->email,
                'password' => 'wrong-password',
                'device_name' => 'iphone-15',
            ]);
        }

        $response = $this->postJson(route('api.v1.auth.login'), [
            'email' => $customer->email,
            'password' => 'wrong-password',
            'device_name' => 'iphone-15',
        ]);

        $response->assertTooManyRequests();
    }
}
