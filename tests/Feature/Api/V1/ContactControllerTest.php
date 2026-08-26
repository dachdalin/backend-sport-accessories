<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_message_is_stored(): void
    {
        $payload = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '555-1234',
            'subject' => 'Order question',
            'message' => 'Where is my order?',
        ];

        $response = $this->postJson(route('api.v1.contacts.store'), $payload);

        $response
            ->assertCreated()
            ->assertJsonPath('data.name', 'Jane Doe')
            ->assertJsonPath('data.email', 'jane@example.com')
            ->assertJsonStructure(['data' => ['id', 'name', 'email', 'phone', 'subject', 'message', 'created_at']]);

        $this->assertDatabaseHas('contacts', [
            'email' => 'jane@example.com',
            'status' => false,
        ]);
    }

    public function test_contact_message_requires_required_fields(): void
    {
        $response = $this->postJson(route('api.v1.contacts.store'), []);

        $response->assertUnprocessable()->assertJsonValidationErrors(['name', 'email', 'subject', 'message']);
    }

    public function test_contact_message_requires_valid_email(): void
    {
        $response = $this->postJson(route('api.v1.contacts.store'), [
            'name' => 'Jane Doe',
            'email' => 'not-an-email',
            'subject' => 'Order question',
            'message' => 'Where is my order?',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_contact_does_not_expose_reply_or_status(): void
    {
        $response = $this->postJson(route('api.v1.contacts.store'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'subject' => 'Order question',
            'message' => 'Where is my order?',
        ]);

        $response->assertJsonMissingPath('data.reply')->assertJsonMissingPath('data.status');
    }
}
