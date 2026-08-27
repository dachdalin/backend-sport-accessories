<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class MessageService
{
    /**
     * List every other staff user with their last message and unread count,
     * most recently active conversation first.
     *
     * @return Collection<int, User>
     */
    public function conversations(User $current): Collection
    {
        $lastMessagesByPartner = Message::query()
            ->where('sender_id', $current->id)
            ->orWhere('receiver_id', $current->id)
            ->latest('id')
            ->get()
            ->groupBy(fn (Message $message) => $message->sender_id === $current->id
                ? $message->receiver_id
                : $message->sender_id)
            ->map(fn (Collection $messages) => $messages->first());

        $unreadCounts = Message::query()
            ->where('receiver_id', $current->id)
            ->whereNull('read_at')
            ->selectRaw('sender_id, COUNT(*) as unread_count')
            ->groupBy('sender_id')
            ->pluck('unread_count', 'sender_id');

        return User::query()
            ->whereKeyNot($current->id)
            ->get()
            ->map(function (User $user) use ($lastMessagesByPartner, $unreadCounts) {
                $user->setAttribute('last_message', $lastMessagesByPartner->get($user->id));
                $user->setAttribute('unread_count', (int) ($unreadCounts->get($user->id) ?? 0));

                return $user;
            })
            ->sortByDesc(fn (User $user) => $user->last_message?->created_at ?? $user->created_at)
            ->values();
    }

    /**
     * The most recent messages between two users, oldest first.
     *
     * @return Collection<int, Message>
     */
    public function thread(User $current, User $other, int $limit = 100): Collection
    {
        return Message::query()
            ->with('sender:id,name')
            ->where(function ($query) use ($current, $other) {
                $query->where('sender_id', $current->id)->where('receiver_id', $other->id);
            })
            ->orWhere(function ($query) use ($current, $other) {
                $query->where('sender_id', $other->id)->where('receiver_id', $current->id);
            })
            ->latest('id')
            ->limit($limit)
            ->get()
            ->sortBy('id')
            ->values();
    }

    /**
     * Mark every unread message from $other to $current as read.
     */
    public function markRead(User $current, User $other): void
    {
        Message::query()
            ->where('sender_id', $other->id)
            ->where('receiver_id', $current->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
