<?php

namespace Tests\Feature;

use App\Models\OfflinePaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class OfflinePaymentMethodControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_offline_payment_methods_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        OfflinePaymentMethod::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('offline-payment-methods.index'));

        $response->assertOk();
    }

    public function test_guests_cannot_view_offline_payment_methods(): void
    {
        $response = $this->get(route('offline-payment-methods.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_offline_payment_method_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('offline-payment-methods.create'));

        $response->assertOk();
    }

    public function test_offline_payment_method_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('offline-payment-methods.store'), [
                'method_name' => 'Bank Transfer',
                'method_fields' => 'Account name, account number',
                'method_informations' => 'Transfer to the account below.',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('offline-payment-methods.index'));

        $method = OfflinePaymentMethod::sole();

        $this->assertSame('Bank Transfer', $method->method_name);
        $this->assertTrue($method->status);
    }

    public function test_offline_payment_method_name_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('offline-payment-methods.store'), [
                'method_name' => '',
                'method_fields' => 'Fields',
                'method_informations' => 'Info',
            ]);

        $response->assertSessionHasErrors('method_name');
    }

    public function test_offline_payment_method_name_must_be_unique_when_created(): void
    {
        $user = User::factory()->create();
        OfflinePaymentMethod::factory()->create(['method_name' => 'Bank Transfer']);

        $response = $this
            ->actingAs($user)
            ->post(route('offline-payment-methods.store'), [
                'method_name' => 'Bank Transfer',
                'method_fields' => 'Fields',
                'method_informations' => 'Info',
            ]);

        $response->assertSessionHasErrors('method_name');
    }

    public function test_offline_payment_method_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $method = OfflinePaymentMethod::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('offline-payment-methods.edit', $method));

        $response->assertOk();
    }

    public function test_offline_payment_method_can_be_updated(): void
    {
        $user = User::factory()->create();
        $method = OfflinePaymentMethod::factory()->create(['status' => false]);

        $response = $this
            ->actingAs($user)
            ->put(route('offline-payment-methods.update', $method), [
                'method_name' => 'Updated name',
                'method_fields' => 'Updated fields',
                'method_informations' => 'Updated info',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('offline-payment-methods.index'));

        $method->refresh();

        $this->assertSame('Updated name', $method->method_name);
        $this->assertTrue($method->status);
    }

    public function test_offline_payment_method_name_must_be_unique_when_updated(): void
    {
        $user = User::factory()->create();
        OfflinePaymentMethod::factory()->create(['method_name' => 'Bank Transfer']);
        $method = OfflinePaymentMethod::factory()->create(['method_name' => 'Cash on Delivery']);

        $response = $this
            ->actingAs($user)
            ->put(route('offline-payment-methods.update', $method), [
                'method_name' => 'Bank Transfer',
                'method_fields' => $method->method_fields,
                'method_informations' => $method->method_informations,
            ]);

        $response->assertSessionHasErrors('method_name');
    }

    public function test_offline_payment_method_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $method = OfflinePaymentMethod::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('offline-payment-methods.destroy', $method));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('offline-payment-methods.index'));

        $this->assertModelMissing($method);
    }
}
