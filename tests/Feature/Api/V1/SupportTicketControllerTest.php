<?php

namespace Tests\Feature\Api\V1;

use App\Models\Customer;
use App\Models\SupportTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SupportTicketControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_support_tickets(): void
    {
        $response = $this->getJson(route('api.v1.support-tickets.index'));

        $response->assertUnauthorized();
    }

    public function test_authenticated_customer_own_tickets_are_listed(): void
    {
        $customer = Customer::factory()->create();
        SupportTicket::factory()->count(3)->create(['customer_id' => $customer->id]);
        SupportTicket::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')->getJson(route('api.v1.support-tickets.index'));

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'subject', 'type', 'priority', 'description', 'attachment_url', 'reply', 'status', 'created_at']]]);
    }

    public function test_authenticated_customer_can_create_support_ticket(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')->postJson(route('api.v1.support-tickets.store'), [
            'subject' => 'Order not delivered',
            'type' => 'billing',
            'priority' => 'high',
            'description' => 'My order has not arrived yet.',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.subject', 'Order not delivered')
            ->assertJsonPath('data.status', 'open');

        $this->assertDatabaseHas('support_tickets', [
            'customer_id' => $customer->id,
            'subject' => 'Order not delivered',
            'status' => 'open',
        ]);
    }

    public function test_authenticated_customer_can_create_support_ticket_with_attachment(): void
    {
        Storage::fake('public');

        $customer = Customer::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')->postJson(route('api.v1.support-tickets.store'), [
            'subject' => 'Broken item received',
            'priority' => 'medium',
            'description' => 'The item arrived damaged.',
            'attachment' => UploadedFile::fake()->image('damage.jpg'),
        ]);

        $response->assertCreated();

        $ticket = SupportTicket::query()->where('customer_id', $customer->id)->firstOrFail();
        Storage::disk('public')->assertExists($ticket->attachment);
    }

    public function test_support_ticket_creation_requires_subject_priority_and_description(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')->postJson(route('api.v1.support-tickets.store'), []);

        $response->assertUnprocessable()->assertJsonValidationErrors(['subject', 'priority', 'description']);
    }

    public function test_authenticated_customer_can_view_own_support_ticket_with_admin_reply(): void
    {
        $customer = Customer::factory()->create();
        $supportTicket = SupportTicket::factory()->create([
            'customer_id' => $customer->id,
            'reply' => 'We are looking into this.',
            'status' => 'answered',
        ]);

        $response = $this->actingAs($customer, 'sanctum')->getJson(route('api.v1.support-tickets.show', $supportTicket));

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $supportTicket->id)
            ->assertJsonPath('data.reply', 'We are looking into this.')
            ->assertJsonPath('data.status', 'answered');
    }

    public function test_customer_cannot_view_another_customers_support_ticket(): void
    {
        $customer = Customer::factory()->create();
        $supportTicket = SupportTicket::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')->getJson(route('api.v1.support-tickets.show', $supportTicket));

        $response->assertNotFound();
    }

    public function test_missing_support_ticket_is_not_found(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')->getJson(route('api.v1.support-tickets.show', 999999));

        $response->assertNotFound();
    }
}
