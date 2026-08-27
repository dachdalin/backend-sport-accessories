<?php

namespace App\Notifications;

use App\Notifications\Channels\PlasGateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerPasswordResetCode extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var int
     */
    public $tries = 3;

    /**
     * @var array<int, int>
     */
    public $backoff = [10, 30, 60];

    /**
     * @param  'email'|'phone'  $channel
     */
    public function __construct(public readonly string $code, public readonly string $channel) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $this->channel === 'phone' ? [PlasGateChannel::class] : ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Your password reset code'))
            ->greeting(__('Hello :name', ['name' => $notifiable->name]))
            ->line(__('Your password reset code is: :code', ['code' => $this->code]))
            ->line(__('This code will expire in 10 minutes.'))
            ->line(__('If you did not request a password reset, no further action is required.'));
    }

    public function toPlasGate(object $notifiable): string
    {
        return __('Your verification code is :code. It expires in 10 minutes.', ['code' => $this->code]);
    }
}
