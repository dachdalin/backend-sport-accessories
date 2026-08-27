<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Notifications\NewOrderPlaced;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_mark_a_single_notification_as_read(): void
    {
        $user = User::factory()->create();
        Notification::send($user, new NewOrderPlaced(Order::factory()->create()));
        $notification = $user->unreadNotifications->sole();

        $response = $this->actingAs($user)->post(route('notifications.read', $notification));

        $response->assertRedirect();
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_admin_cannot_mark_another_admins_notification_as_read(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Notification::send($otherUser, new NewOrderPlaced(Order::factory()->create()));
        $notification = $otherUser->unreadNotifications->sole();

        $response = $this->actingAs($user)->post(route('notifications.read', $notification));

        $response->assertNotFound();
        $this->assertNull($notification->fresh()->read_at);
    }

    public function test_admin_can_mark_all_notifications_as_read(): void
    {
        $user = User::factory()->create();
        Notification::send($user, new NewOrderPlaced(Order::factory()->create()));
        Notification::send($user, new NewOrderPlaced(Order::factory()->create()));

        $response = $this->actingAs($user)->post(route('notifications.read-all'));

        $response->assertRedirect();
        $this->assertSame(0, $user->unreadNotifications()->count());
    }

    public function test_guest_cannot_mark_notifications_as_read(): void
    {
        $response = $this->post(route('notifications.read-all'));

        $response->assertRedirect(route('login'));
    }
}
