<?php

namespace App\Notifications;

use App\Models\Order;
use App\Notifications\Channels\TelegramChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;

class NewOrderPlaced extends Notification implements ShouldQueue
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

    public function __construct(public readonly Order $order) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $notifiable instanceof AnonymousNotifiable
            ? [TelegramChannel::class]
            : ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'order',
            'title' => __('New order #:number', ['number' => $this->order->order_number]),
            'subtitle' => "{$this->order->customer_name} · \${$this->order->order_amount}",
            'href' => route('orders.edit', $this->order),
        ];
    }

    public function toTelegram(object $notifiable): string
    {
        return sprintf(
            "🛒 <b>New order #%s</b>\nCustomer: %s\nAmount: \$%s\nPayment: %s",
            $this->order->order_number,
            $this->order->customer_name,
            $this->order->order_amount,
            $this->order->payment_method ?? 'N/A',
        );
    }
}
