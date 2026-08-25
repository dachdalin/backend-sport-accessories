<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_contacts_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Contact::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('contacts.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('contacts/Index')
                ->has('contacts.data', 3),
            );
    }

    public function test_contacts_index_page_is_paginated(): void
    {
        $user = User::factory()->create();
        Contact::factory()->count(16)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('contacts.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('contacts/Index')
                ->has('contacts.data', 15),
            );
    }

    public function test_contact_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('contacts.create'));

        $response->assertOk();
    }

    public function test_contact_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('contacts.store'), [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'phone' => '+1 555 000 0000',
                'subject' => 'Question about an order',
                'message' => 'Where is my order?',
                'status' => '0',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('contacts.index'));

        $contact = Contact::sole();

        $this->assertSame('Jane Doe', $contact->name);
        $this->assertSame('Question about an order', $contact->subject);
        $this->assertFalse($contact->status);
    }

    public function test_contact_name_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('contacts.store'), [
                'name' => '',
                'email' => 'jane@example.com',
                'subject' => 'Subject',
                'message' => 'Message body',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_contact_message_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('contacts.store'), [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'subject' => 'Subject',
                'message' => '',
            ]);

        $response->assertSessionHasErrors('message');
    }

    public function test_contact_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $contact = Contact::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('contacts.edit', $contact));

        $response->assertOk();
    }

    public function test_contact_can_be_updated(): void
    {
        $user = User::factory()->create();
        $contact = Contact::factory()->create(['status' => false]);

        $response = $this
            ->actingAs($user)
            ->put(route('contacts.update', $contact), [
                'name' => $contact->name,
                'email' => $contact->email,
                'subject' => $contact->subject,
                'message' => $contact->message,
                'reply' => 'Thanks for reaching out, here is an update.',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('contacts.index'));

        $contact->refresh();

        $this->assertTrue($contact->status);
        $this->assertSame('Thanks for reaching out, here is an update.', $contact->reply);
    }

    public function test_contact_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $contact = Contact::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('contacts.destroy', $contact));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('contacts.index'));

        $this->assertModelMissing($contact);
    }

    public function test_guest_cannot_access_contacts(): void
    {
        $response = $this->get(route('contacts.index'));

        $response->assertRedirect(route('login'));
    }
}
