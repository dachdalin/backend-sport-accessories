<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class TelegramChannel
{
    /**
     * Send the given notification to a Telegram chat via the Bot API.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toTelegram')) {
            return;
        }

        $chatId = $notifiable->routeNotificationFor('telegram', $notification);
        $botToken = config('services.telegram.bot_token');

        if (! $chatId || ! $botToken) {
            return;
        }

        Http::timeout(5)
            ->connectTimeout(3)
            ->retry([100, 500])
            ->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $notification->toTelegram($notifiable),
                'parse_mode' => 'HTML',
            ])
            ->throw();
    }
}
