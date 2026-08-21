<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_page_is_displayed_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->where('filter', 'today')
                ->missing('stats')
                ->missing('salesChart')
                ->missing('topProducts'),
            );
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_dashboard_stats_only_include_orders_from_today(): void
    {
        $user = User::factory()->create();

        $this->makeOrderOn(now(), '100.00');
        $this->makeOrderOn(now()->subDays(2), '500.00');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertInertia(fn (Assert $page) => $page
            ->loadDeferredProps(fn (Assert $reload) => $reload
                ->where('stats.revenue', '100.00')
                ->where('stats.orders', 1),
            ),
        );
    }

    public function test_dashboard_can_filter_by_this_month(): void
    {
        $user = User::factory()->create();

        $this->makeOrderOn(now()->startOfMonth(), '75.00');
        $this->makeOrderOn(now()->subMonths(2), '999.00');

        $response = $this->actingAs($user)->get(route('dashboard', ['filter' => 'this_month']));

        $response->assertInertia(fn (Assert $page) => $page
            ->where('filter', 'this_month')
            ->loadDeferredProps(fn (Assert $reload) => $reload
                ->where('stats.revenue', '75.00'),
            ),
        );
    }

    public function test_dashboard_can_filter_by_custom_range(): void
    {
        $user = User::factory()->create();

        $this->makeOrderOn(now()->subDays(10), '40.00');
        $this->makeOrderOn(now()->subDays(2), '20.00');

        $response = $this->actingAs($user)->get(route('dashboard', [
            'filter' => 'custom',
            'from' => now()->subDays(11)->toDateString(),
            'to' => now()->subDays(9)->toDateString(),
        ]));

        $response->assertInertia(fn (Assert $page) => $page
            ->loadDeferredProps(fn (Assert $reload) => $reload
                ->where('stats.revenue', '40.00'),
            ),
        );
    }

    public function test_dashboard_ranks_top_products_by_revenue(): void
    {
        $user = User::factory()->create();

        $topProduct = Product::factory()->create(['name' => 'Bestseller']);
        $otherProduct = Product::factory()->create(['name' => 'Runner Up']);

        $order = Order::factory()->create();
        $order->forceFill(['created_at' => now()])->save();

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $topProduct->id,
            'quantity' => 2,
            'unit_price' => 100,
            'subtotal' => 200,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $otherProduct->id,
            'quantity' => 1,
            'unit_price' => 50,
            'subtotal' => 50,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertInertia(fn (Assert $page) => $page
            ->loadDeferredProps(fn (Assert $reload) => $reload
                ->where('topProducts.0.name', 'Bestseller')
                ->where('topProducts.0.revenue', '200.00')
                ->where('topProducts.0.share', 100)
                ->where('topProducts.1.name', 'Runner Up'),
            ),
        );
    }

    private function makeOrderOn(\DateTimeInterface|string $date, string $amount): Order
    {
        $order = Order::factory()->create(['order_amount' => $amount]);
        $order->forceFill(['created_at' => $date])->save();

        return $order;
    }
}
