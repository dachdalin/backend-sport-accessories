<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * Resolve a filter key (and optional custom bounds) into a concrete date range.
     *
     * @return array{0: CarbonInterface, 1: CarbonInterface}
     */
    public function resolveRange(string $filter, ?string $from, ?string $to): array
    {
        return match ($filter) {
            'this_week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'this_month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'this_year' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            'custom' => [
                $from ? Carbon::parse($from)->startOfDay() : Carbon::now()->startOfDay(),
                $to ? Carbon::parse($to)->endOfDay() : Carbon::now()->endOfDay(),
            ],
            default => [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()],
        };
    }

    /**
     * @return array{revenue: string, orders: int, averageOrderValue: string, customers: int}
     */
    public function stats(CarbonInterface $start, CarbonInterface $end): array
    {
        $orders = Order::query()->whereBetween('created_at', [$start, $end]);

        $revenue = (clone $orders)->sum('order_amount');
        $count = (clone $orders)->count();
        $customers = (clone $orders)->distinct()->count('customer_email');

        return [
            'revenue' => number_format((float) $revenue, 2, '.', ''),
            'orders' => $count,
            'averageOrderValue' => number_format($count > 0 ? $revenue / $count : 0, 2, '.', ''),
            'customers' => $customers,
        ];
    }

    /**
     * Revenue grouped into buckets sized to fit the requested range.
     *
     * @return array<int, array{label: string, revenue: string}>
     */
    public function salesChart(CarbonInterface $start, CarbonInterface $end, string $filter): array
    {
        $granularity = match ($filter) {
            'today' => 'hour',
            'this_week', 'this_month' => 'day',
            'this_year' => 'month',
            default => $start->diffInDays($end) > 60 ? 'month' : 'day',
        };

        $orders = Order::query()
            ->whereBetween('created_at', [$start, $end])
            ->get(['order_amount', 'created_at']);

        return match ($granularity) {
            'hour' => $this->bucketByHour($orders, $start),
            'month' => $this->bucketByMonth($orders, $start, $end),
            default => $this->bucketByDay($orders, $start, $end),
        };
    }

    /**
     * Top products by revenue within the range.
     *
     * @return array<int, array{id: int, name: string, thumbnail: string, thumbnailStorageType: string, unitsSold: int, revenue: string, share: float}>
     */
    public function topProducts(CarbonInterface $start, CarbonInterface $end, int $limit = 5): array
    {
        $rows = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->selectRaw('products.id, products.name, products.thumbnail, products.thumbnail_storage_type, sum(order_items.quantity) as units_sold, sum(order_items.subtotal) as revenue')
            ->groupBy('products.id', 'products.name', 'products.thumbnail', 'products.thumbnail_storage_type')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get();

        $topRevenue = (float) ($rows->first()->revenue ?? 0);

        return $rows
            ->map(fn ($row) => [
                'id' => $row->id,
                'name' => $row->name,
                'thumbnail' => $row->thumbnail,
                'thumbnailStorageType' => $row->thumbnail_storage_type,
                'unitsSold' => (int) $row->units_sold,
                'revenue' => number_format((float) $row->revenue, 2, '.', ''),
                'share' => $topRevenue > 0 ? round(((float) $row->revenue / $topRevenue) * 100, 1) : 0.0,
            ])
            ->all();
    }

    /**
     * @param  Collection<int, Order>  $orders
     * @return array<int, array{label: string, revenue: string}>
     */
    private function bucketByHour(Collection $orders, CarbonInterface $day): array
    {
        $totals = array_fill(0, 24, 0.0);

        foreach ($orders as $order) {
            $totals[(int) $order->created_at->format('G')] += (float) $order->order_amount;
        }

        return collect($totals)
            ->map(fn (float $total, int $hour) => [
                'label' => Carbon::createFromTime($hour)->format('g A'),
                'revenue' => number_format($total, 2, '.', ''),
            ])
            ->values()
            ->all();
    }

    /**
     * @param  Collection<int, Order>  $orders
     * @return array<int, array{label: string, revenue: string}>
     */
    private function bucketByDay(Collection $orders, CarbonInterface $start, CarbonInterface $end): array
    {
        $totals = [];
        $cursor = $start->copy()->startOfDay();

        while ($cursor->lte($end)) {
            $totals[$cursor->format('Y-m-d')] = 0.0;
            $cursor->addDay();
        }

        foreach ($orders as $order) {
            $key = $order->created_at->format('Y-m-d');

            if (array_key_exists($key, $totals)) {
                $totals[$key] += (float) $order->order_amount;
            }
        }

        return collect($totals)
            ->map(fn (float $total, string $date) => [
                'label' => Carbon::parse($date)->format('M j'),
                'revenue' => number_format($total, 2, '.', ''),
            ])
            ->values()
            ->all();
    }

    /**
     * @param  Collection<int, Order>  $orders
     * @return array<int, array{label: string, revenue: string}>
     */
    private function bucketByMonth(Collection $orders, CarbonInterface $start, CarbonInterface $end): array
    {
        $totals = [];
        $cursor = $start->copy()->startOfMonth();

        while ($cursor->lte($end)) {
            $totals[$cursor->format('Y-m')] = 0.0;
            $cursor->addMonth();
        }

        foreach ($orders as $order) {
            $key = $order->created_at->format('Y-m');

            if (array_key_exists($key, $totals)) {
                $totals[$key] += (float) $order->order_amount;
            }
        }

        return collect($totals)
            ->map(fn (float $total, string $month) => [
                'label' => Carbon::createFromFormat('Y-m', $month)->format('M'),
                'revenue' => number_format($total, 2, '.', ''),
            ])
            ->values()
            ->all();
    }
}
