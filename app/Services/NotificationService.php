<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Review;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Build a summary of store activity that needs the given admin's attention: their unread
     * notifications (e.g. new orders) plus other pending items that aren't tracked as notifications.
     *
     * @return array{items: array<int, array<string, mixed>>, total: int}
     */
    public function summary(User $user): array
    {
        $items = $this->unreadNotifications($user)
            ->concat($this->pendingReviews())
            ->concat($this->openTickets())
            ->concat($this->unreadContacts())
            ->sortByDesc('timestamp')
            ->take(8)
            ->values();

        return [
            'items' => $items->all(),
            'total' => $user->unreadNotifications()->count()
                + Review::query()->where('status', 'pending')->count()
                + SupportTicket::query()->where('status', 'open')->count()
                + Contact::query()->where('status', false)->count(),
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function unreadNotifications(User $user): Collection
    {
        return $user->unreadNotifications()
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (DatabaseNotification $notification) => [
                'id' => $notification->id,
                'type' => $notification->data['type'] ?? 'order',
                'title' => $notification->data['title'] ?? '',
                'subtitle' => $notification->data['subtitle'] ?? '',
                'timestamp' => $notification->created_at,
                'href' => $notification->data['href'] ?? '#',
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
