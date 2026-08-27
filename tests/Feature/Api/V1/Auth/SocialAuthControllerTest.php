<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SocialAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    private function fakeGoogleToken(array $overrides = []): void
    {
        Http::fake([
            'oauth2.googleapis.com/*' => Http::response(array_merge([
                'aud' => 'test-client-id',
                'sub' => 'google-user-1',
                'email' => 'jane@example.com',
                'email_verified' => 'true',
                'name' => 'Jane Doe',
            ], $overrides)),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function telegramPayload(array $overrides = []): array
    {
        Config::set('services.telegram.bot_token', 'test-bot-token');

        $payload = array_merge([
            'id' => 123456,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'username' => 'janedoe',
            'auth_date' => now()->timestamp,
        ], $overrides);

        $checkString = collect($payload)
            ->reject(fn ($value) => $value === null)
            ->sortKeys()
            ->map(fn ($value, $key) => "{$key}={$value}")
            ->implode("\n");

        $secretKey = hash('sha256', 'test-bot-token', true);
        $payload['hash'] = hash_hmac('sha256', $checkString, $secretKey);

        return $payload;
    }

    public function test_new_customer_can_sign_in_with_google(): void
    {
        $this->fakeGoogleToken();

        $response = $this->postJson(route('api.v1.auth.google'), ['id_token' => 'valid-token']);

        $response
            ->assertOk()
            ->assertJsonStructure(['customer' => ['id', 'name', 'email'], 'token'])
            ->assertJsonPath('customer.email', 'jane@example.com');

        $this->assertDatabaseHas('customers', ['email' => 'jane@example.com', 'google_id' => 'google-user-1']);
    }

    public function test_returning_google_customer_reuses_the_same_account(): void
    {
        $this->fakeGoogleToken();

        $first = $this->postJson(route('api.v1.auth.google'), ['id_token' => 'valid-token'])->json('customer.id');
        $second = $this->postJson(route('api.v1.auth.google'), ['id_token' => 'valid-token'])->json('customer.id');

        $this->assertSame($first, $second);
        $this->assertDatabaseCount('customers', 1);
    }

    public function test_google_sign_in_links_to_an_existing_account_with_the_same_email(): void
    {
        $customer = Customer::factory()->create(['email' => 'jane@example.com']);
        $this->fakeGoogleToken();

        $response = $this->postJson(route('api.v1.auth.google'), ['id_token' => 'valid-token']);

        $response->assertOk()->assertJsonPath('customer.id', $customer->id);
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'google_id' => 'google-user-1']);
    }

    public function test_google_sign_in_rejects_an_invalid_token(): void
    {
        Http::fake(['oauth2.googleapis.com/*' => Http::response(['error' => 'invalid_token'], 400)]);

        $response = $this->postJson(route('api.v1.auth.google'), ['id_token' => 'bad-token']);

        $response->assertUnprocessable()->assertJsonValidationErrors('id_token');
    }

    public function test_google_sign_in_rejects_a_token_issued_for_another_application(): void
    {
        Config::set('services.google.client_id', 'expected-client-id');
        $this->fakeGoogleToken(['aud' => 'someone-elses-client-id']);

        $response = $this->postJson(route('api.v1.auth.google'), ['id_token' => 'valid-token']);

        $response->assertUnprocessable()->assertJsonValidationErrors('id_token');
    }

    public function test_google_sign_in_rejects_an_unverified_email(): void
    {
        $this->fakeGoogleToken(['email_verified' => 'false']);

        $response = $this->postJson(route('api.v1.auth.google'), ['id_token' => 'valid-token']);

        $response->assertUnprocessable()->assertJsonValidationErrors('id_token');
    }

    public function test_deactivated_customer_cannot_sign_in_with_google(): void
    {
        Customer::factory()->create(['email' => 'jane@example.com', 'status' => false]);
        $this->fakeGoogleToken();

        $response = $this->postJson(route('api.v1.auth.google'), ['id_token' => 'valid-token']);

        $response->assertUnprocessable()->assertJsonValidationErrors('id_token');
    }

    public function test_new_customer_can_sign_in_with_telegram(): void
    {
        $payload = $this->telegramPayload();

        $response = $this->postJson(route('api.v1.auth.telegram'), $payload);

        $response
            ->assertOk()
            ->assertJsonStructure(['customer' => ['id', 'name', 'email'], 'token'])
            ->assertJsonPath('customer.name', 'Jane Doe');

        $this->assertDatabaseHas('customers', ['telegram_id' => '123456']);
    }

    public function test_returning_telegram_customer_reuses_the_same_account(): void
    {
        $payload = $this->telegramPayload();
        $this->postJson(route('api.v1.auth.telegram'), $payload);

        $payload2 = $this->telegramPayload(['auth_date' => now()->addSecond()->timestamp]);
        $this->postJson(route('api.v1.auth.telegram'), $payload2);

        $this->assertDatabaseCount('customers', 1);
    }

    public function test_telegram_sign_in_rejects_a_tampered_hash(): void
    {
        $payload = $this->telegramPayload();
        $payload['first_name'] = 'Tampered';

        $response = $this->postJson(route('api.v1.auth.telegram'), $payload);

        $response->assertUnprocessable()->assertJsonValidationErrors('hash');
    }

    public function test_telegram_sign_in_rejects_stale_auth_data(): void
    {
        $payload = $this->telegramPayload(['auth_date' => now()->subDays(2)->timestamp]);

        $response = $this->postJson(route('api.v1.auth.telegram'), $payload);

        $response->assertUnprocessable()->assertJsonValidationErrors('auth_date');
    }

    public function test_telegram_sign_in_is_rejected_when_not_configured(): void
    {
        $payload = $this->telegramPayload();
        Config::set('services.telegram.bot_token', null);

        $response = $this->postJson(route('api.v1.auth.telegram'), $payload);

        $response->assertUnprocessable()->assertJsonValidationErrors('hash');
    }

    public function test_deactivated_customer_cannot_sign_in_with_telegram(): void
    {
        $payload = $this->telegramPayload(['id' => 999]);
        Customer::factory()->create(['telegram_id' => '999', 'status' => false]);

        $response = $this->postJson(route('api.v1.auth.telegram'), $payload);

        $response->assertUnprocessable()->assertJsonValidationErrors('hash');
    }
}
