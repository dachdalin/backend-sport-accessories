<?php

namespace Tests\Feature;

use App\Models\HelpTopic;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class HelpTopicControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_help_topics_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        HelpTopic::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('help-topics.index'));

        $response->assertOk();
    }

    public function test_guests_cannot_view_help_topics(): void
    {
        $response = $this->get(route('help-topics.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_help_topic_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('help-topics.create'));

        $response->assertOk();
    }

    public function test_help_topic_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('help-topics.store'), [
                'type' => 'shipping',
                'question' => 'How do I track my order?',
                'answer' => 'Use the tracking link in your confirmation email.',
                'ranking' => 5,
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('help-topics.index'));

        $helpTopic = HelpTopic::sole();

        $this->assertSame('shipping', $helpTopic->type);
        $this->assertSame('How do I track my order?', $helpTopic->question);
        $this->assertSame(5, $helpTopic->ranking);
        $this->assertTrue($helpTopic->status);
    }

    public function test_help_topic_defaults_type_and_ranking_when_omitted(): void
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->post(route('help-topics.store'), [
                'question' => 'What is your return policy?',
                'answer' => 'Items can be returned within 30 days.',
            ]);

        $helpTopic = HelpTopic::sole();

        $this->assertSame('default', $helpTopic->type);
        $this->assertSame(1, $helpTopic->ranking);
    }

    public function test_help_topic_question_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('help-topics.store'), [
                'question' => '',
                'answer' => 'An answer.',
            ]);

        $response->assertSessionHasErrors('question');
    }

    public function test_help_topic_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $helpTopic = HelpTopic::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('help-topics.edit', $helpTopic));

        $response->assertOk();
    }

    public function test_help_topic_can_be_updated(): void
    {
        $user = User::factory()->create();
        $helpTopic = HelpTopic::factory()->create(['status' => true]);

        $response = $this
            ->actingAs($user)
            ->put(route('help-topics.update', $helpTopic), [
                'type' => 'billing',
                'question' => 'Updated question?',
                'answer' => 'Updated answer.',
                'ranking' => 10,
                'status' => '0',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('help-topics.index'));

        $helpTopic->refresh();

        $this->assertSame('Updated question?', $helpTopic->question);
        $this->assertSame(10, $helpTopic->ranking);
        $this->assertFalse($helpTopic->status);
    }

    public function test_help_topic_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $helpTopic = HelpTopic::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('help-topics.destroy', $helpTopic));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('help-topics.index'));

        $this->assertModelMissing($helpTopic);
    }
}
