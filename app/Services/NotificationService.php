<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Order;
use App\Models\Review;
use App\Models\SupportTicket;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Build a summary of store activity that needs admin attention.
     *
     * @return array{items: array<int, array<string, mixed>>, total: int}
     */
    public function summary(): array
    {
        $items = collect()
            ->concat($this->pendingOrders())
            ->concat($this->pendingReviews())
            ->concat($this->openTickets())
            ->concat($this->unreadContacts())
            ->sortByDesc('timestamp')
            ->take(8)
            ->values();

        return [
            'items' => $items->all(),
            'total' => Order::query()->where('order_status', 'pending')->count()
                + Review::query()->where('status', 'pending')->count()
                + SupportTicket::query()->where('status', 'open')->count()
                + Contact::query()->where('status', false)->count(),
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function pendingOrders(): Collection
    {
        return Order::query()
            ->where('order_status', 'pending')
            ->latest()
            ->limit(5)
            ->get(['id', 'order_number', 'customer_name', 'order_amount', 'created_at'])
            ->map(fn (Order $order) => [
                'type' => 'order',
                'title' => "Order #{$order->order_number}",
                'subtitle' => "{$order->customer_name} · \${$order->order_amount}",
                'timestamp' => $order->created_at,
                'href' => route('orders.edit', $order),
            ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function pendingReviews(): Collection
    {
        return Review::query()
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get(['id', 'customer_name', 'rating', 'created_at'])
            ->map(fn (Review $review) => [
                'type' => 'review',
                'title' => "{$review->rating}-star review from {$review->customer_name}",
                'subtitle' => 'Awaiting moderation',
                'timestamp' => $review->created_at,
                'href' => route('reviews.edit', $review),
            ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function openTickets(): Collection
    {
        return SupportTicket::query()
            ->where('status', 'open')
            ->latest()
            ->limit(5)
            ->get(['id', 'subject', 'priority', 'created_at'])
            ->map(fn (SupportTicket $ticket) => [
                'type' => 'ticket',
                'title' => $ticket->subject,
                'subtitle' => ucfirst($ticket->priority).' priority',
                'timestamp' => $ticket->created_at,
                'href' => route('support-tickets.edit', $ticket),
            ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function unreadContacts(): Collection
    {
        return Contact::query()
            ->where('status', false)
            ->latest()
            ->limit(5)
            ->get(['id', 'name', 'subject', 'created_at'])
            ->map(fn (Contact $contact) => [
                'type' => 'contact',
                'title' => $contact->subject,
                'subtitle' => "From {$contact->name}",
                'timestamp' => $contact->created_at,
                'href' => route('contacts.edit', $contact),
            ]);
    }
}
