<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Notifications\Channels\TelegramChannel;
use App\Notifications\NewOrderPlaced;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramChannelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_posts_the_notification_text_to_the_telegram_bot_api(): void
    {
        Config::set('services.telegram.bot_token', 'test-token');
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true])]);

        $order = Order::factory()->create(['order_number' => 'ORD-TEST1234']);
        $notifiable = (new AnonymousNotifiable)->route('telegram', '999');

        (new TelegramChannel)->send($notifiable, new NewOrderPlaced($order));

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://api.telegram.org/bottest-token/sendMessage'
                && $request['chat_id'] === '999'
                && str_contains($request['text'], 'ORD-TEST1234');
        });
    }

    public function test_it_does_nothing_when_the_bot_token_is_not_configured(): void
    {
        Config::set('services.telegram.bot_token', null);
        Http::fake();

        $order = Order::factory()->create();
        $notifiable = (new AnonymousNotifiable)->route('telegram', '999');

        (new TelegramChannel)->send($notifiable, new NewOrderPlaced($order));

        Http::assertNothingSent();
    }

    public function test_it_does_nothing_when_no_chat_id_is_routed(): void
    {
        Config::set('services.telegram.bot_token', 'test-token');
        Http::fake();

        $order = Order::factory()->create();
        $notifiable = new AnonymousNotifiable;

        (new TelegramChannel)->send($notifiable, new NewOrderPlaced($order));

        Http::assertNothingSent();
    }
}
