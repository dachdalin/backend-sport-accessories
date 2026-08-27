<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Models\User;
use App\Notifications\NewOrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendNewOrderNotifications implements ShouldQueue
{
    /**
     * @var int
     */
    public $tries = 3;

    /**
     * @var array<int, int>
     */
    public $backoff = [10, 30, 60];

    public function handle(OrderPlaced $event): void
    {
        Notification::send(User::all(), new NewOrderPlaced($event->order));

        $chatId = config('services.telegram.chat_id');

        if ($chatId && config('services.telegram.bot_token')) {
            Notification::route('telegram', $chatId)->notify(new NewOrderPlaced($event->order));
        }
    }
}
