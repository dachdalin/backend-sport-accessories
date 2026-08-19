<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CurrencyControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_currencies_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Currency::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('currencies.index'));

        $response->assertOk();
    }

    public function test_guests_cannot_view_currencies(): void
    {
        $response = $this->get(route('currencies.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_currency_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('currencies.create'));

        $response->assertOk();
    }

    public function test_currency_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('currencies.store'), [
                'name' => 'US Dollar',
                'symbol' => '$',
                'code' => 'usd',
                'exchange_rate' => '1.0000',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('currencies.index'));

        $currency = Currency::sole();

        $this->assertSame('US Dollar', $currency->name);
        $this->assertSame('USD', $currency->code);
        $this->assertTrue($currency->status);
    }

    public function test_currency_code_is_uppercased_on_create(): void
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->post(route('currencies.store'), [
                'name' => 'Euro',
                'symbol' => '€',
                'code' => 'eur',
                'exchange_rate' => '0.9000',
            ]);

        $this->assertDatabaseHas('currencies', ['code' => 'EUR']);
    }

    public function test_currency_name_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('currencies.store'), [
                'name' => '',
                'symbol' => '$',
                'code' => 'USD',
                'exchange_rate' => '1',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_currency_code_must_be_unique_when_created(): void
    {
        $user = User::factory()->create();
        Currency::factory()->create(['code' => 'USD']);

        $response = $this
            ->actingAs($user)
            ->post(route('currencies.store'), [
                'name' => 'US Dollar',
                'symbol' => '$',
                'code' => 'usd',
                'exchange_rate' => '1',
            ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_currency_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $currency = Currency::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('currencies.edit', $currency));

        $response->assertOk();
    }

    public function test_currency_can_be_updated(): void
    {
        $user = User::factory()->create();
        $currency = Currency::factory()->create(['status' => false]);

        $response = $this
            ->actingAs($user)
            ->put(route('currencies.update', $currency), [
                'name' => 'Updated name',
                'symbol' => '£',
                'code' => 'gbp',
                'exchange_rate' => '0.8000',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('currencies.index'));

        $currency->refresh();

        $this->assertSame('Updated name', $currency->name);
        $this->assertSame('GBP', $currency->code);
        $this->assertTrue($currency->status);
    }

    public function test_currency_code_must_be_unique_when_updated(): void
    {
        $user = User::factory()->create();
        Currency::factory()->create(['code' => 'USD']);
        $currency = Currency::factory()->create(['code' => 'EUR']);

        $response = $this
            ->actingAs($user)
            ->put(route('currencies.update', $currency), [
                'name' => $currency->name,
                'symbol' => $currency->symbol,
                'code' => 'usd',
                'exchange_rate' => $currency->exchange_rate,
            ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_currency_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $currency = Currency::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('currencies.destroy', $currency));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('currencies.index'));

        $this->assertModelMissing($currency);
    }
}
