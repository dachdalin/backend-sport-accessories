<?php

namespace Tests\Feature;

use App\Events\OrderPlaced;
use App\Listeners\SendNewOrderNotifications;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingAddress;
use App\Models\User;
use App\Notifications\NewOrderPlaced;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NewOrderNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_backend_order_creation_dispatches_order_placed_event(): void
    {
        Event::fake([OrderPlaced::class]);

        $user = User::factory()->create();
        $product = Product::factory()->create(['unit_price' => '20.00']);

        $this->actingAs($user)->post(route('orders.store'), [
            'customer_name' => 'Jane Doe',
            'shipping_address' => '123 Main St',
            'order_status' => 'pending',
            'payment_status' => 'unpaid',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2, 'unit_price' => '20.00'],
            ],
        ])->assertSessionHasNoErrors();

        Event::assertDispatched(
            OrderPlaced::class,
            fn (OrderPlaced $event) => $event->order->customer_name === 'Jane Doe',
        );
    }

    public function test_api_checkout_dispatches_order_placed_event(): void
    {
        Event::fake([OrderPlaced::class]);

        $customer = Customer::factory()->create();
        $shippingAddress = ShippingAddress::factory()->create(['customer_id' => $customer->id]);
        $product = Product::factory()->create(['current_stock' => 5]);
        CartItem::factory()->create(['customer_id' => $customer->id, 'product_id' => $product->id, 'quantity' => 1]);

        $this->actingAs($customer, 'sanctum')
            ->postJson(route('api.v1.orders.store'), ['shipping_address_id' => $shippingAddress->id])
            ->assertCreated();

        Event::assertDispatched(
            OrderPlaced::class,
            fn (OrderPlaced $event) => $event->order->customer_id === $customer->id,
        );
    }

    public function test_listener_sends_a_database_notification_to_every_admin_user(): void
    {
        Notification::fake();

        $users = User::factory()->count(2)->create();
        $order = Order::factory()->create();

        (new SendNewOrderNotifications)->handle(new OrderPlaced($order));

        Notification::assertSentTo(
            $users,
            NewOrderPlaced::class,
            fn (NewOrderPlaced $notification) => $notification->order->is($order),
        );
    }

    public function test_listener_sends_a_telegram_notification_when_configured(): void
    {
        Config::set('services.telegram.bot_token', 'test-token');
        Config::set('services.telegram.chat_id', '12345');

        Notification::fake();

        $order = Order::factory()->create();

        (new SendNewOrderNotifications)->handle(new OrderPlaced($order));

        Notification::assertSentOnDemand(
            NewOrderPlaced::class,
            fn (NewOrderPlaced $notification, array $channels, $notifiable) => $notifiable->routeNotificationFor('telegram') === '12345',
        );
    }

    public function test_listener_skips_telegram_when_not_configured(): void
    {
        Config::set('services.telegram.bot_token', null);
        Config::set('services.telegram.chat_id', null);

        Notification::fake();

        $users = User::factory()->count(2)->create();
        $order = Order::factory()->create();

        (new SendNewOrderNotifications)->handle(new OrderPlaced($order));

        Notification::assertSentTimes(NewOrderPlaced::class, $users->count());
    }
}
