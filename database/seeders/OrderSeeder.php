<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * @var array<int, array{name: string, price: float}>
     */
    private array $catalog = [
        ['name' => 'Pro Training Football', 'price' => 34.99],
        ['name' => 'Carbon Fiber Tennis Racket', 'price' => 129.00],
        ['name' => 'Adjustable Dumbbell Set 20kg', 'price' => 189.50],
        ['name' => 'Yoga Mat Extra Thick', 'price' => 24.99],
        ['name' => 'Running Shoes Elite', 'price' => 89.99],
        ['name' => 'Basketball Official Size 7', 'price' => 29.99],
        ['name' => 'Resistance Bands Set', 'price' => 19.99],
        ['name' => 'Cycling Helmet Aero', 'price' => 74.50],
        ['name' => 'Boxing Gloves Pro', 'price' => 54.00],
        ['name' => 'Swim Goggles Anti-Fog', 'price' => 14.99],
        ['name' => 'Golf Club Driver', 'price' => 219.00],
        ['name' => 'Hiking Backpack 40L', 'price' => 99.99],
        ['name' => 'Skateboard Complete', 'price' => 64.99],
        ['name' => 'Badminton Racket Set', 'price' => 39.99],
        ['name' => 'Fitness Tracker Watch', 'price' => 79.00],
    ];

    /**
     * @var array<int, array{name: string, email: string}>
     */
    private array $customers = [
        ['name' => 'Liam Carter', 'email' => 'liam.carter@example.com'],
        ['name' => 'Olivia Bennett', 'email' => 'olivia.bennett@example.com'],
        ['name' => 'Noah Reyes', 'email' => 'noah.reyes@example.com'],
        ['name' => 'Emma Whitfield', 'email' => 'emma.whitfield@example.com'],
        ['name' => 'Ethan Brooks', 'email' => 'ethan.brooks@example.com'],
        ['name' => 'Ava Mercer', 'email' => 'ava.mercer@example.com'],
        ['name' => 'Mason Delgado', 'email' => 'mason.delgado@example.com'],
        ['name' => 'Sophia Nakamura', 'email' => 'sophia.nakamura@example.com'],
        ['name' => 'Lucas Ferreira', 'email' => 'lucas.ferreira@example.com'],
        ['name' => 'Isabella Novak', 'email' => 'isabella.novak@example.com'],
        ['name' => 'James Okafor', 'email' => 'james.okafor@example.com'],
        ['name' => 'Mia Andersson', 'email' => 'mia.andersson@example.com'],
        ['name' => 'Benjamin Wu', 'email' => 'benjamin.wu@example.com'],
        ['name' => 'Charlotte Silva', 'email' => 'charlotte.silva@example.com'],
        ['name' => 'Henry Osei', 'email' => 'henry.osei@example.com'],
    ];

    /**
     * Seed a year of order history so the dashboard has data for every filter.
     */
    public function run(): void
    {
        if (Order::query()->exists()) {
            return;
        }

        $products = collect($this->catalog)->map(fn (array $item) => Product::factory()->create([
            'name' => $item['name'],
            'slug' => Str::slug($item['name']),
            'unit_price' => $item['price'],
            'current_stock' => fake()->numberBetween(15, 120),
        ]));

        $today = Carbon::now()->startOfDay();

        for ($daysAgo = 365; $daysAgo > 30; $daysAgo--) {
            if (fake()->boolean(35)) {
                $this->createOrdersForDay($today->copy()->subDays($daysAgo), fake()->numberBetween(1, 2), $products);
            }
        }

        for ($daysAgo = 30; $daysAgo > 0; $daysAgo--) {
            $this->createOrdersForDay($today->copy()->subDays($daysAgo), fake()->numberBetween(1, 4), $products);
        }

        // Guarantee a handful of orders today, since that's the dashboard's default filter.
        $this->createOrdersForDay($today, fake()->numberBetween(3, 6), $products);
    }

    /**
     * @param  Collection<int, Product>  $products
     */
    private function createOrdersForDay(Carbon $day, int $count, Collection $products): void
    {
        for ($i = 0; $i < $count; $i++) {
            $placedAt = $day->copy()
                ->addHours(fake()->numberBetween(8, 21))
                ->addMinutes(fake()->numberBetween(0, 59));

            $this->createOrder($placedAt, $products);
        }
    }

    /**
     * @param  Collection<int, Product>  $products
     */
    private function createOrder(Carbon $placedAt, Collection $products): void
    {
        $customer = fake()->randomElement($this->customers);

        $order = Order::factory()->create([
            'customer_name' => $customer['name'],
            'customer_email' => $customer['email'],
            'order_status' => fake()->randomElement(['pending', 'processing', 'shipped', 'delivered']),
            'payment_status' => fake()->randomElement(['paid', 'unpaid']),
            'order_amount' => 0,
        ]);

        $order->forceFill(['created_at' => $placedAt, 'updated_at' => $placedAt])->save();

        $total = 0.0;

        foreach ($products->random(fake()->numberBetween(1, 4)) as $product) {
            $quantity = fake()->numberBetween(1, 3);
            $subtotal = round((float) $product->unit_price * $quantity, 2);
            $total += $subtotal;

            OrderItem::factory()->create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'unit_price' => $product->unit_price,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ]);
        }

        $order->update(['order_amount' => $total]);
    }
}
