<?php

namespace Tests\Feature\Api\V1;

use App\Models\Faq;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaqControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_faqs_are_listed(): void
    {
        Faq::factory()->count(3)->create(['status' => true]);
        Faq::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.faqs.index'));

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'question', 'answer', 'sort_order']]]);
    }

    public function test_faq_list_is_paginated(): void
    {
        Faq::factory()->count(16)->create(['status' => true]);

        $response = $this->getJson(route('api.v1.faqs.index'));

        $response->assertOk()->assertJsonCount(15, 'data');
    }

    public function test_active_faq_can_be_shown(): void
    {
        $faq = Faq::factory()->create(['status' => true]);

        $response = $this->getJson(route('api.v1.faqs.show', $faq));

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $faq->id)
            ->assertJsonPath('data.question', $faq->question);
    }

    public function test_inactive_faq_is_not_found(): void
    {
        $faq = Faq::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.faqs.show', $faq));

        $response->assertNotFound();
    }

    public function test_missing_faq_is_not_found(): void
    {
        $response = $this->getJson(route('api.v1.faqs.show', 999999));

        $response->assertNotFound();
    }
}
