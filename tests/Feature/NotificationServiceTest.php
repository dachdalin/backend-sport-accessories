<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Order;
use App\Models\Review;
use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\NewOrderPlaced;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_summary_includes_the_admins_unread_order_notification(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['order_number' => 'ORD-ABC12345']);
        Notification::send($user, new NewOrderPlaced($order));

        $summary = app(NotificationService::class)->summary($user);

        $this->assertSame(1, $summary['total']);
        $this->assertCount(1, $summary['items']);
        $this->assertSame('order', $summary['items'][0]['type']);
        $this->assertStringContainsString('ORD-ABC12345', $summary['items'][0]['title']);
        $this->assertNotEmpty($summary['items'][0]['id']);
    }

    public function test_summary_does_not_include_another_admins_notifications(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Notification::send($otherUser, new NewOrderPlaced(Order::factory()->create()));

        $summary = app(NotificationService::class)->summary($user);

        $this->assertSame(0, $summary['total']);
        $this->assertCount(0, $summary['items']);
    }

    public function test_summary_merges_order_notifications_with_other_pending_activity(): void
    {
        $user = User::factory()->create();
        Notification::send($user, new NewOrderPlaced(Order::factory()->create()));
        Review::factory()->create(['status' => 'pending']);
        SupportTicket::factory()->create(['status' => 'open']);
        Contact::factory()->create(['status' => false]);

        $summary = app(NotificationService::class)->summary($user);

        $this->assertSame(4, $summary['total']);
        $this->assertCount(4, $summary['items']);
        $types = collect($summary['items'])->pluck('type')->all();
        $this->assertEqualsCanonicalizing(['order', 'review', 'ticket', 'contact'], $types);
    }

    public function test_read_notifications_are_excluded_from_the_summary(): void
    {
        $user = User::factory()->create();
        Notification::send($user, new NewOrderPlaced(Order::factory()->create()));
        $user->unreadNotifications->sole()->markAsRead();

        $summary = app(NotificationService::class)->summary($user);

        $this->assertSame(0, $summary['total']);
        $this->assertCount(0, $summary['items']);
    }
}
