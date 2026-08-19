<?php

namespace Tests\Feature;

use App\Models\SoftCredential;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SoftCredentialControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_credentials_index_page_never_exposes_the_raw_value(): void
    {
        $user = User::factory()->create();
        $credential = SoftCredential::factory()->create(['key' => 'STRIPE_SECRET_KEY', 'value' => 'sk_test_super_secret']);

        $response = $this->actingAs($user)->get(route('credentials.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('credentials/Index')
                ->has('credentials', 1)
                ->where('credentials.0.id', $credential->id)
                ->where('credentials.0.key', 'STRIPE_SECRET_KEY')
                ->where('credentials.0.is_configured', true)
                ->missing('credentials.0.value'),
            );

        $response->assertDontSee('sk_test_super_secret', false);
    }

    public function test_guests_cannot_view_credentials(): void
    {
        $response = $this->get(route('credentials.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_credential_can_be_created_and_is_encrypted_at_rest(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('credentials.store'), [
            'key' => 'STRIPE_SECRET_KEY',
            'value' => 'sk_test_super_secret',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('credentials.index'));

        $credential = SoftCredential::query()->where('key', 'STRIPE_SECRET_KEY')->firstOrFail();

        $this->assertSame('sk_test_super_secret', $credential->value);

        $rawValue = $credential->getRawOriginal('value');
        $this->assertNotSame('sk_test_super_secret', $rawValue);
        $this->assertSame('sk_test_super_secret', Crypt::decryptString($rawValue));
    }

    public function test_value_is_required_when_creating(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('credentials.store'), [
            'key' => 'STRIPE_SECRET_KEY',
        ]);

        $response->assertSessionHasErrors('value');
    }

    public function test_key_must_be_screaming_snake_case(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('credentials.store'), [
            'key' => 'stripe secret key',
            'value' => 'sk_test_super_secret',
        ]);

        $response->assertSessionHasErrors('key');
    }

    public function test_duplicate_key_is_rejected(): void
    {
        $user = User::factory()->create();
        SoftCredential::factory()->create(['key' => 'STRIPE_SECRET_KEY']);

        $response = $this->actingAs($user)->post(route('credentials.store'), [
            'key' => 'STRIPE_SECRET_KEY',
            'value' => 'sk_test_another_secret',
        ]);

        $response->assertSessionHasErrors('key');
    }

    public function test_key_can_be_updated_without_changing_the_value(): void
    {
        $user = User::factory()->create();
        $credential = SoftCredential::factory()->create(['key' => 'STRIPE_SECRET_KEY', 'value' => 'sk_test_super_secret']);

        $response = $this->actingAs($user)->put(route('credentials.update', $credential), [
            'key' => 'STRIPE_LIVE_KEY',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('credentials.index'));

        $credential->refresh();

        $this->assertSame('STRIPE_LIVE_KEY', $credential->key);
        $this->assertSame('sk_test_super_secret', $credential->value);
    }

    public function test_value_can_be_replaced_when_updating(): void
    {
        $user = User::factory()->create();
        $credential = SoftCredential::factory()->create(['key' => 'STRIPE_SECRET_KEY', 'value' => 'sk_test_super_secret']);

        $response = $this->actingAs($user)->put(route('credentials.update', $credential), [
            'key' => 'STRIPE_SECRET_KEY',
            'value' => 'sk_test_rotated_secret',
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertSame('sk_test_rotated_secret', $credential->refresh()->value);
    }

    public function test_credential_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $credential = SoftCredential::factory()->create();

        $response = $this->actingAs($user)->delete(route('credentials.destroy', $credential));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('credentials.index'));

        $this->assertModelMissing($credential);
    }
}
