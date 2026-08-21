<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SupportTicketControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_support_tickets_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        SupportTicket::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('support-tickets.index'));

        $response->assertOk();
    }

    public function test_support_ticket_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('support-tickets.create'));

        $response->assertOk();
    }

    public function test_support_ticket_can_be_created(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('support-tickets.store'), [
                'customer_id' => $customer->id,
                'subject' => 'Order has not arrived',
                'type' => 'shipping',
                'priority' => 'high',
                'description' => 'The customer has not received their order.',
                'status' => 'open',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('support-tickets.index'));

        $supportTicket = SupportTicket::sole();

        $this->assertSame($customer->id, $supportTicket->customer_id);
        $this->assertSame('Order has not arrived', $supportTicket->subject);
        $this->assertSame('high', $supportTicket->priority);
        $this->assertSame('open', $supportTicket->status);
        $this->assertNull($supportTicket->attachment);
    }

    public function test_support_ticket_can_be_created_with_an_attachment(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $customer = Customer::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('support-tickets.store'), [
                'customer_id' => $customer->id,
                'subject' => 'Broken item',
                'priority' => 'medium',
                'description' => 'The item arrived broken, photo attached.',
                'status' => 'open',
                'attachment' => UploadedFile::fake()->image('proof.jpg'),
            ]);

        $response->assertSessionHasNoErrors();

        $supportTicket = SupportTicket::sole();

        $this->assertNotNull($supportTicket->attachment);
        Storage::disk('public')->assertExists($supportTicket->attachment);
    }

    public function test_support_ticket_customer_must_exist(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('support-tickets.store'), [
                'customer_id' => 999,
                'subject' => 'Order has not arrived',
                'priority' => 'low',
                'description' => 'Missing order.',
                'status' => 'open',
            ]);

        $response->assertSessionHasErrors('customer_id');
    }

    public function test_support_ticket_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $supportTicket = SupportTicket::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('support-tickets.edit', $supportTicket));

        $response->assertOk();
    }

    public function test_support_ticket_can_be_updated(): void
    {
        $user = User::factory()->create();
        $supportTicket = SupportTicket::factory()->create(['status' => 'open']);

        $response = $this
            ->actingAs($user)
            ->put(route('support-tickets.update', $supportTicket), [
                'customer_id' => $supportTicket->customer_id,
                'subject' => $supportTicket->subject,
                'priority' => 'high',
                'description' => $supportTicket->description,
                'reply' => 'We have shipped a replacement.',
                'status' => 'answered',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('support-tickets.index'));

        $supportTicket->refresh();

        $this->assertSame('answered', $supportTicket->status);
        $this->assertSame('high', $supportTicket->priority);
        $this->assertSame('We have shipped a replacement.', $supportTicket->reply);
    }

    public function test_support_ticket_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $supportTicket = SupportTicket::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('support-tickets.destroy', $supportTicket));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('support-tickets.index'));

        $this->assertModelMissing($supportTicket);
    }

    public function test_guest_cannot_access_support_tickets(): void
    {
        $response = $this->get(route('support-tickets.index'));

        $response->assertRedirect(route('login'));
    }
}
