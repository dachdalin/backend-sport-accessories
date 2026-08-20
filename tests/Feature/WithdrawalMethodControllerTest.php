<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WithdrawalMethod;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class WithdrawalMethodControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_withdrawal_methods_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        WithdrawalMethod::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('withdrawal-methods.index'));

        $response->assertOk();
    }

    public function test_guests_cannot_view_withdrawal_methods(): void
    {
        $response = $this->get(route('withdrawal-methods.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_withdrawal_method_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('withdrawal-methods.create'));

        $response->assertOk();
    }

    public function test_withdrawal_method_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('withdrawal-methods.store'), [
                'method_name' => 'Bank Transfer',
                'method_fields' => 'Account name, account number',
                'is_default' => '1',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('withdrawal-methods.index'));

        $method = WithdrawalMethod::sole();

        $this->assertSame('Bank Transfer', $method->method_name);
        $this->assertTrue($method->is_default);
        $this->assertTrue($method->status);
    }

    public function test_withdrawal_method_name_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('withdrawal-methods.store'), [
                'method_name' => '',
                'method_fields' => 'Fields',
            ]);

        $response->assertSessionHasErrors('method_name');
    }

    public function test_withdrawal_method_name_must_be_unique_when_created(): void
    {
        $user = User::factory()->create();
        WithdrawalMethod::factory()->create(['method_name' => 'Bank Transfer']);

        $response = $this
            ->actingAs($user)
            ->post(route('withdrawal-methods.store'), [
                'method_name' => 'Bank Transfer',
                'method_fields' => 'Fields',
            ]);

        $response->assertSessionHasErrors('method_name');
    }

    public function test_withdrawal_method_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $method = WithdrawalMethod::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('withdrawal-methods.edit', $method));

        $response->assertOk();
    }

    public function test_withdrawal_method_can_be_updated(): void
    {
        $user = User::factory()->create();
        $method = WithdrawalMethod::factory()->create(['status' => true, 'is_default' => false]);

        $response = $this
            ->actingAs($user)
            ->put(route('withdrawal-methods.update', $method), [
                'method_name' => 'Updated name',
                'method_fields' => 'Updated fields',
                'is_default' => '1',
                'status' => '0',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('withdrawal-methods.index'));

        $method->refresh();

        $this->assertSame('Updated name', $method->method_name);
        $this->assertTrue($method->is_default);
        $this->assertFalse($method->status);
    }

    public function test_withdrawal_method_name_must_be_unique_when_updated(): void
    {
        $user = User::factory()->create();
        WithdrawalMethod::factory()->create(['method_name' => 'Bank Transfer']);
        $method = WithdrawalMethod::factory()->create(['method_name' => 'PayPal']);

        $response = $this
            ->actingAs($user)
            ->put(route('withdrawal-methods.update', $method), [
                'method_name' => 'Bank Transfer',
                'method_fields' => $method->method_fields,
            ]);

        $response->assertSessionHasErrors('method_name');
    }

    public function test_withdrawal_method_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $method = WithdrawalMethod::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('withdrawal-methods.destroy', $method));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('withdrawal-methods.index'));

        $this->assertModelMissing($method);
    }
}
