<?php

namespace Tests\Feature\Api\V1;

use App\Models\HelpTopic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HelpTopicControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_help_topics_are_listed(): void
    {
        HelpTopic::factory()->count(3)->create(['status' => true]);
        HelpTopic::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.help-topics.index'));

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'type', 'question', 'answer', 'ranking']]]);
    }

    public function test_help_topic_list_is_paginated(): void
    {
        HelpTopic::factory()->count(16)->create(['status' => true]);

        $response = $this->getJson(route('api.v1.help-topics.index'));

        $response->assertOk()->assertJsonCount(15, 'data');
    }

    public function test_active_help_topic_can_be_shown(): void
    {
        $helpTopic = HelpTopic::factory()->create(['status' => true]);

        $response = $this->getJson(route('api.v1.help-topics.show', $helpTopic));

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $helpTopic->id)
            ->assertJsonPath('data.question', $helpTopic->question);
    }

    public function test_inactive_help_topic_is_not_found(): void
    {
        $helpTopic = HelpTopic::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.help-topics.show', $helpTopic));

        $response->assertNotFound();
    }

    public function test_missing_help_topic_is_not_found(): void
    {
        $response = $this->getJson(route('api.v1.help-topics.show', 999999));

        $response->assertNotFound();
    }
}
