<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RefundRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RefundRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_refund_requests_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        RefundRequest::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('refund-requests.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('refund-requests/Index')
                ->has('refundRequests.data', 3),
            );
    }

    public function test_refund_requests_index_page_is_paginated(): void
    {
        $user = User::factory()->create();
        RefundRequest::factory()->count(16)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('refund-requests.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('refund-requests/Index')
                ->has('refundRequests.data', 15),
            );
    }

    public function test_refund_request_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('refund-requests.create'));

        $response->assertOk();
    }

    public function test_refund_request_can_be_created(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();
        $orderItem = OrderItem::factory()->create(['order_id' => $order->id]);

        $response = $this
            ->actingAs($user)
            ->post(route('refund-requests.store'), [
                'order_id' => $order->id,
                'order_item_id' => $orderItem->id,
                'amount' => '25.00',
                'reason' => 'Item arrived damaged.',
                'status' => 'pending',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('refund-requests.index'));

        $refundRequest = RefundRequest::sole();

        $this->assertSame($order->id, $refundRequest->order_id);
        $this->assertSame($orderItem->id, $refundRequest->order_item_id);
        $this->assertSame('25.00', $refundRequest->amount);
        $this->assertSame('pending', $refundRequest->status->value);
    }

    public function test_refund_request_can_be_created_for_whole_order(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('refund-requests.store'), [
                'order_id' => $order->id,
                'amount' => '50.00',
                'reason' => 'Customer changed their mind.',
                'status' => 'pending',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('refund-requests.index'));

        $refundRequest = RefundRequest::sole();

        $this->assertSame($order->id, $refundRequest->order_id);
        $this->assertNull($refundRequest->order_item_id);
    }

    public function test_refund_request_order_must_exist(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('refund-requests.store'), [
                'order_id' => 999999,
                'amount' => '25.00',
                'reason' => 'Item arrived damaged.',
                'status' => 'pending',
            ]);

        $response->assertSessionHasErrors('order_id');
    }

    public function test_refund_request_order_item_must_belong_to_order(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();
        $otherOrder = Order::factory()->create();
        $otherOrderItem = OrderItem::factory()->create(['order_id' => $otherOrder->id]);

        $response = $this
            ->actingAs($user)
            ->post(route('refund-requests.store'), [
                'order_id' => $order->id,
                'order_item_id' => $otherOrderItem->id,
                'amount' => '25.00',
                'reason' => 'Item arrived damaged.',
                'status' => 'pending',
            ]);

        $response->assertSessionHasErrors('order_item_id');
    }

    public function test_refund_request_reason_is_required(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('refund-requests.store'), [
                'order_id' => $order->id,
                'amount' => '25.00',
                'reason' => '',
                'status' => 'pending',
            ]);

        $response->assertSessionHasErrors('reason');
    }

    public function test_refund_request_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $refundRequest = RefundRequest::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('refund-requests.edit', $refundRequest));

        $response->assertOk();
    }

    public function test_refund_request_can_be_updated(): void
    {
        $user = User::factory()->create();
        $refundRequest = RefundRequest::factory()->create(['status' => 'pending']);

        $response = $this
            ->actingAs($user)
            ->put(route('refund-requests.update', $refundRequest), [
                'order_id' => $refundRequest->order_id,
                'amount' => '30.00',
                'reason' => 'Updated reason.',
                'status' => 'approved',
                'admin_note' => 'Refund approved by admin.',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('refund-requests.index'));

        $refundRequest->refresh();

        $this->assertSame('30.00', $refundRequest->amount);
        $this->assertSame('approved', $refundRequest->status->value);
        $this->assertSame('Refund approved by admin.', $refundRequest->admin_note);
    }

    public function test_refund_request_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $refundRequest = RefundRequest::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('refund-requests.destroy', $refundRequest));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('refund-requests.index'));

        $this->assertModelMissing($refundRequest);
    }

    public function test_guest_cannot_access_refund_requests(): void
    {
        $response = $this->get(route('refund-requests.index'));

        $response->assertRedirect(route('login'));
    }
}
