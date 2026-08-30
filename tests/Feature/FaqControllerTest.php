<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaqControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_faqs_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Faq::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('faqs.index'));

        $response->assertOk();
    }

    public function test_faq_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('faqs.store'), [
                'question' => 'What is your return policy?',
                'answer' => 'You can return any item within 30 days.',
                'sort_order' => '5',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('faqs.index'));

        $faq = Faq::sole();

        $this->assertSame('What is your return policy?', $faq->question);
        $this->assertSame(5, $faq->sort_order);
        $this->assertTrue($faq->status);
    }

    public function test_faq_question_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('faqs.store'), [
                'question' => '',
                'answer' => 'Some answer.',
            ]);

        $response->assertSessionHasErrors('question');
    }

    public function test_faq_answer_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('faqs.store'), [
                'question' => 'Some question?',
                'answer' => '',
            ]);

        $response->assertSessionHasErrors('answer');
    }

    public function test_faq_can_be_updated(): void
    {
        $user = User::factory()->create();
        $faq = Faq::factory()->create(['status' => true]);

        $response = $this
            ->actingAs($user)
            ->put(route('faqs.update', $faq), [
                'question' => 'Updated question?',
                'answer' => $faq->answer,
                'sort_order' => '10',
                'status' => '0',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('faqs.index'));

        $faq->refresh();

        $this->assertSame('Updated question?', $faq->question);
        $this->assertSame(10, $faq->sort_order);
        $this->assertFalse($faq->status);
    }

    public function test_faq_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $faq = Faq::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('faqs.destroy', $faq));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('faqs.index'));

        $this->assertModelMissing($faq);
    }
}
