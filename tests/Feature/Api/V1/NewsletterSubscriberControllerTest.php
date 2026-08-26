<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterSubscriberControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_is_subscribed(): void
    {
        $response = $this->postJson(route('api.v1.newsletter-subscribers.store'), [
            'email' => 'jane@example.com',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.email', 'jane@example.com')
            ->assertJsonStructure(['data' => ['id', 'email', 'created_at']]);

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'jane@example.com',
            'status' => true,
        ]);
    }

    public function test_email_is_required(): void
    {
        $response = $this->postJson(route('api.v1.newsletter-subscribers.store'), []);

        $response->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_email_must_be_valid(): void
    {
        $response = $this->postJson(route('api.v1.newsletter-subscribers.store'), [
            'email' => 'not-an-email',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_email_cannot_be_subscribed_twice(): void
    {
        $this->postJson(route('api.v1.newsletter-subscribers.store'), [
            'email' => 'jane@example.com',
        ]);

        $response = $this->postJson(route('api.v1.newsletter-subscribers.store'), [
            'email' => 'jane@example.com',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('email');
    }
}
