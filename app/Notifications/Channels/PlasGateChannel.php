<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class PlasGateChannel
{
    /**
     * Send the given notification as an SMS via the PlasGate OTP API.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toPlasGate')) {
            return;
        }

        $phone = $notifiable->routeNotificationFor('plasgate', $notification) ?? $notifiable->phone ?? null;

        if (! $phone) {
            return;
        }

        Http::timeout(5)
            ->connectTimeout(3)
            ->retry([100, 500])
            ->withHeaders([
                'private_key' => config('services.plasgate.private_key'),
                'secret_key' => config('services.plasgate.secret_key'),
            ])
            ->post(config('services.plasgate.base_url'), [
                'sender' => config('services.plasgate.sender'),
                'to' => $phone,
                'content' => $notification->toPlasGate($notifiable),
            ])
            ->throw();
    }
}
