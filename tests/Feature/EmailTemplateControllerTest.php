<?php

namespace Tests\Feature;

use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailTemplateControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_templates_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        EmailTemplate::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('email-templates.index'));

        $response->assertOk();
    }

    public function test_email_template_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('email-templates.create'));

        $response->assertOk();
    }

    public function test_email_template_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('email-templates.store'), [
                'name' => 'Order confirmation',
                'subject' => 'Your order has been confirmed',
                'body' => 'Thanks for your order, {{customer_name}}.',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('email-templates.index'));

        $emailTemplate = EmailTemplate::sole();

        $this->assertSame('Order confirmation', $emailTemplate->name);
        $this->assertSame('Your order has been confirmed', $emailTemplate->subject);
        $this->assertTrue($emailTemplate->status);
    }

    public function test_email_template_name_must_be_unique(): void
    {
        $user = User::factory()->create();
        $existing = EmailTemplate::factory()->create(['name' => 'Order confirmation']);

        $response = $this
            ->actingAs($user)
            ->post(route('email-templates.store'), [
                'name' => $existing->name,
                'subject' => 'Duplicate name',
                'body' => 'Body text.',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_email_template_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $emailTemplate = EmailTemplate::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('email-templates.edit', $emailTemplate));

        $response->assertOk();
    }

    public function test_email_template_can_be_updated(): void
    {
        $user = User::factory()->create();
        $emailTemplate = EmailTemplate::factory()->create(['status' => true]);

        $response = $this
            ->actingAs($user)
            ->put(route('email-templates.update', $emailTemplate), [
                'name' => $emailTemplate->name,
                'subject' => 'Updated subject line',
                'body' => 'Updated body content.',
                'status' => '0',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('email-templates.index'));

        $emailTemplate->refresh();

        $this->assertSame('Updated subject line', $emailTemplate->subject);
        $this->assertSame('Updated body content.', $emailTemplate->body);
        $this->assertFalse($emailTemplate->status);
    }

    public function test_email_template_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $emailTemplate = EmailTemplate::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('email-templates.destroy', $emailTemplate));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('email-templates.index'));

        $this->assertModelMissing($emailTemplate);
    }

    public function test_guest_cannot_access_email_templates(): void
    {
        $response = $this->get(route('email-templates.index'));

        $response->assertRedirect(route('login'));
    }
}
