<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Models\Customer;
use App\Models\PasswordResetCode;
use App\Notifications\Channels\PlasGateChannel;
use App\Notifications\CustomerPasswordResetCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_sends_a_mail_code_when_email_is_given(): void
    {
        Notification::fake();

        $customer = Customer::factory()->create();

        $response = $this->postJson(route('api.v1.auth.forgot-password'), ['email' => $customer->email]);

        $response->assertOk();

        Notification::assertSentTo($customer, CustomerPasswordResetCode::class, function ($notification) {
            return $notification->channel === 'email' && $notification->via((object) []) === ['mail'];
        });

        $this->assertDatabaseCount('customer_password_resets', 1);
    }

    public function test_forgot_password_sends_an_otp_via_plasgate_when_phone_is_given(): void
    {
        Notification::fake();

        $customer = Customer::factory()->create(['phone' => '855123456789']);

        $response = $this->postJson(route('api.v1.auth.forgot-password'), ['phone' => $customer->phone]);

        $response->assertOk();

        Notification::assertSentTo($customer, CustomerPasswordResetCode::class, function ($notification) {
            return $notification->channel === 'phone' && $notification->via((object) []) === [PlasGateChannel::class];
        });
    }

    public function test_forgot_password_gives_a_generic_response_when_no_account_matches(): void
    {
        Notification::fake();

        $response = $this->postJson(route('api.v1.auth.forgot-password'), ['email' => 'nobody@example.com']);

        $response->assertOk()->assertJsonStructure(['message']);

        Notification::assertNothingSent();
        $this->assertDatabaseCount('customer_password_resets', 0);
    }

    public function test_forgot_password_requires_email_or_phone(): void
    {
        $response = $this->postJson(route('api.v1.auth.forgot-password'), []);

        $response->assertUnprocessable()->assertJsonValidationErrors(['email', 'phone']);
    }

    public function test_requesting_a_new_code_invalidates_the_previous_unused_one(): void
    {
        Notification::fake();

        $customer = Customer::factory()->create();

        $this->postJson(route('api.v1.auth.forgot-password'), ['email' => $customer->email]);
        $this->postJson(route('api.v1.auth.forgot-password'), ['email' => $customer->email]);

        $this->assertDatabaseCount('customer_password_resets', 1);
    }

    public function test_customer_can_reset_password_with_a_valid_code(): void
    {
        $customer = Customer::factory()->create();
        $code = '123456';
        PasswordResetCode::factory()->create([
            'customer_id' => $customer->id,
            'channel' => 'email',
            'code' => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
        ]);
        $customer->createToken('device-1');

        $response = $this->postJson(route('api.v1.auth.reset-password'), [
            'email' => $customer->email,
            'code' => $code,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertOk();

        $this->assertTrue(Hash::check('new-password', $customer->fresh()->password));
        $this->assertDatabaseCount('personal_access_tokens', 0);
        $this->assertNotNull(PasswordResetCode::first()->used_at);
    }

    public function test_reset_password_rejects_an_incorrect_code(): void
    {
        $customer = Customer::factory()->create();
        PasswordResetCode::factory()->create([
            'customer_id' => $customer->id,
            'channel' => 'email',
            'code' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->postJson(route('api.v1.auth.reset-password'), [
            'email' => $customer->email,
            'code' => '000000',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('code');
    }

    public function test_reset_password_rejects_an_expired_code(): void
    {
        $customer = Customer::factory()->create();
        PasswordResetCode::factory()->create([
            'customer_id' => $customer->id,
            'channel' => 'email',
            'code' => Hash::make('123456'),
            'expires_at' => now()->subMinute(),
        ]);

        $response = $this->postJson(route('api.v1.auth.reset-password'), [
            'email' => $customer->email,
            'code' => '123456',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('code');
    }

    public function test_reset_password_rejects_an_already_used_code(): void
    {
        $customer = Customer::factory()->create();
        PasswordResetCode::factory()->create([
            'customer_id' => $customer->id,
            'channel' => 'email',
            'code' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
            'used_at' => now(),
        ]);

        $response = $this->postJson(route('api.v1.auth.reset-password'), [
            'email' => $customer->email,
            'code' => '123456',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('code');
    }

    public function test_reset_password_rejects_an_unknown_identifier(): void
    {
        $response = $this->postJson(route('api.v1.auth.reset-password'), [
            'email' => 'nobody@example.com',
            'code' => '123456',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('code');
    }

    public function test_reset_password_requires_email_or_phone(): void
    {
        $response = $this->postJson(route('api.v1.auth.reset-password'), [
            'code' => '123456',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['email', 'phone']);
    }
}
