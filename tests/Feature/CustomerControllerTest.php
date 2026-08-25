<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_customers_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Customer::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('customers.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('customers/Index')
                ->has('customers.data', 3),
            );
    }

    public function test_customers_index_page_is_paginated(): void
    {
        $user = User::factory()->create();
        Customer::factory()->count(16)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('customers.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('customers/Index')
                ->has('customers.data', 15),
            );
    }

    public function test_customer_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('customers.create'));

        $response->assertOk();
    }

    public function test_customer_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('customers.store'), [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'phone' => '+1 555 000 0000',
                'address' => '123 Main Street, London',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('customers.index'));

        $customer = Customer::sole();

        $this->assertSame('Jane Doe', $customer->name);
        $this->assertSame('jane@example.com', $customer->email);
        $this->assertTrue($customer->status);
    }

    public function test_customer_name_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('customers.store'), [
                'name' => '',
                'email' => 'jane@example.com',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_customer_email_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('customers.store'), [
                'name' => 'Jane Doe',
                'email' => '',
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_customer_email_must_be_unique(): void
    {
        $user = User::factory()->create();
        Customer::factory()->create(['email' => 'jane@example.com']);

        $response = $this
            ->actingAs($user)
            ->post(route('customers.store'), [
                'name' => 'Another Customer',
                'email' => 'jane@example.com',
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_customer_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('customers.edit', $customer));

        $response->assertOk();
    }

    public function test_customer_can_be_updated(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create(['status' => true]);

        $response = $this
            ->actingAs($user)
            ->put(route('customers.update', $customer), [
                'name' => 'Updated Customer Name',
                'email' => $customer->email,
                'status' => '0',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('customers.index'));

        $customer->refresh();

        $this->assertSame('Updated Customer Name', $customer->name);
        $this->assertFalse($customer->status);
    }

    public function test_customer_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('customers.destroy', $customer));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('customers.index'));

        $this->assertModelMissing($customer);
    }

    public function test_guest_cannot_access_customers(): void
    {
        $response = $this->get(route('customers.index'));

        $response->assertRedirect(route('login'));
    }
}
