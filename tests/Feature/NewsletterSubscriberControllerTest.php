<?php

namespace Tests\Feature;

use App\Mail\NewsletterMail;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class NewsletterSubscriberControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_newsletter_subscribers_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $subscriber = NewsletterSubscriber::factory()->create();

        $response = $this->actingAs($user)->get(route('newsletter-subscribers.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('newsletter-subscribers/Index')
                ->has('subscribers.data', 1)
                ->where('subscribers.data.0.id', $subscriber->id),
            );
    }

    public function test_newsletter_subscribers_index_page_is_paginated(): void
    {
        $user = User::factory()->create();
        NewsletterSubscriber::factory()->count(16)->create();

        $response = $this->actingAs($user)->get(route('newsletter-subscribers.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('newsletter-subscribers/Index')
                ->has('subscribers.data', 15),
            );
    }

    public function test_guests_cannot_view_newsletter_subscribers(): void
    {
        $response = $this->get(route('newsletter-subscribers.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_newsletter_subscriber_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('newsletter-subscribers.store'), [
            'email' => 'jane@example.com',
            'status' => '1',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('newsletter-subscribers.index'));

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'jane@example.com',
            'status' => true,
        ]);
    }

    public function test_newsletter_subscriber_email_must_be_unique_when_created(): void
    {
        $user = User::factory()->create();
        NewsletterSubscriber::factory()->create(['email' => 'jane@example.com']);

        $response = $this->actingAs($user)->post(route('newsletter-subscribers.store'), [
            'email' => 'jane@example.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_newsletter_subscriber_email_must_be_valid(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('newsletter-subscribers.store'), [
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_newsletter_subscriber_can_be_updated(): void
    {
        $user = User::factory()->create();
        $subscriber = NewsletterSubscriber::factory()->create(['status' => true]);

        $response = $this->actingAs($user)->put(route('newsletter-subscribers.update', $subscriber), [
            'email' => 'updated@example.com',
            'status' => '0',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('newsletter-subscribers.index'));

        $subscriber->refresh();

        $this->assertSame('updated@example.com', $subscriber->email);
        $this->assertFalse($subscriber->status);
    }

    public function test_newsletter_subscriber_email_must_be_unique_when_updated(): void
    {
        $user = User::factory()->create();
        NewsletterSubscriber::factory()->create(['email' => 'jane@example.com']);
        $subscriber = NewsletterSubscriber::factory()->create(['email' => 'john@example.com']);

        $response = $this->actingAs($user)->put(route('newsletter-subscribers.update', $subscriber), [
            'email' => 'jane@example.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_newsletter_subscriber_can_keep_its_own_email_when_updated(): void
    {
        $user = User::factory()->create();
        $subscriber = NewsletterSubscriber::factory()->create(['email' => 'jane@example.com']);

        $response = $this->actingAs($user)->put(route('newsletter-subscribers.update', $subscriber), [
            'email' => 'jane@example.com',
            'status' => '1',
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_newsletter_subscriber_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $subscriber = NewsletterSubscriber::factory()->create();

        $response = $this->actingAs($user)->delete(route('newsletter-subscribers.destroy', $subscriber));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('newsletter-subscribers.index'));

        $this->assertModelMissing($subscriber);
    }

    public function test_newsletter_email_can_be_sent_to_a_single_subscriber(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $subscriber = NewsletterSubscriber::factory()->create(['email' => 'jane@example.com', 'status' => true]);

        $response = $this->actingAs($user)->post(route('newsletter-subscribers.send', $subscriber), [
            'subject' => 'Hello there',
            'body' => '<p>New arrivals this week.</p>',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        Mail::assertQueued(NewsletterMail::class, function (NewsletterMail $mail) {
            return $mail->hasTo('jane@example.com')
                && $mail->hasSubject('Hello there')
                && $mail->bodyHtml === '<p>New arrivals this week.</p>';
        });
        Mail::assertQueuedCount(1);
    }

    public function test_newsletter_email_cannot_be_sent_to_an_unsubscribed_subscriber(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $subscriber = NewsletterSubscriber::factory()->create(['status' => false]);

        $response = $this->actingAs($user)->post(route('newsletter-subscribers.send', $subscriber), [
            'subject' => 'Hello there',
            'body' => '<p>New arrivals this week.</p>',
        ]);

        $response->assertRedirect();

        Mail::assertNothingQueued();
    }

    public function test_newsletter_email_requires_a_subject_and_body(): void
    {
        $user = User::factory()->create();
        $subscriber = NewsletterSubscriber::factory()->create(['status' => true]);

        $response = $this->actingAs($user)->post(route('newsletter-subscribers.send', $subscriber), []);

        $response->assertSessionHasErrors(['subject', 'body']);
    }

    public function test_newsletter_email_can_be_sent_to_all_subscribed_subscribers(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        NewsletterSubscriber::factory()->count(2)->create(['status' => true]);
        NewsletterSubscriber::factory()->create(['status' => false]);

        $response = $this->actingAs($user)->post(route('newsletter-subscribers.send-all'), [
            'subject' => 'Monthly update',
            'body' => '<p>Here is what changed.</p>',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        Mail::assertQueuedCount(2);
    }

    public function test_newsletter_email_is_not_sent_when_there_are_no_subscribed_subscribers(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        NewsletterSubscriber::factory()->create(['status' => false]);

        $response = $this->actingAs($user)->post(route('newsletter-subscribers.send-all'), [
            'subject' => 'Monthly update',
            'body' => '<p>Here is what changed.</p>',
        ]);

        $response->assertRedirect();

        Mail::assertNothingQueued();
    }
}
