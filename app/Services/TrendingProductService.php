<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductSearch;
use Illuminate\Support\Collection;

class TrendingProductService
{
    /**
     * The most-searched active products over the last 7 days, ranked by search
     * count, each annotated with its growth versus the 7 days before that.
     *
     * `growth_percent` is null when a product had no searches in the prior
     * week — there's no baseline to compute a percentage change from.
     *
     * @return Collection<int, array{rank: int, product: Product, search_count: int, growth_percent: ?int}>
     */
    public function trending(int $limit = 10): Collection
    {
        $now = now();
        $thisWeekStart = $now->copy()->subDays(7);
        $lastWeekStart = $now->copy()->subDays(14);

        $thisWeekCounts = ProductSearch::query()
            ->where('created_at', '>=', $thisWeekStart)
            ->selectRaw('product_id, COUNT(*) as search_count')
            ->groupBy('product_id')
            ->pluck('search_count', 'product_id');

        $lastWeekCounts = ProductSearch::query()
            ->whereBetween('created_at', [$lastWeekStart, $thisWeekStart])
            ->selectRaw('product_id, COUNT(*) as search_count')
            ->groupBy('product_id')
            ->pluck('search_count', 'product_id');

        // Over-fetch: some of the top IDs by search count may belong to products
        // that were deactivated since, so ranking has a buffer to fall back on.
        $candidateIds = $thisWeekCounts->sortDesc()->take($limit * 2)->keys();

        $products = Product::query()
            ->whereIn('id', $candidateIds)
            ->where('status', true)
            ->get()
            ->keyBy('id');

        return $candidateIds
            ->filter(fn (int $id) => $products->has($id))
            ->take($limit)
            ->values()
            ->map(function (int $id, int $index) use ($products, $thisWeekCounts, $lastWeekCounts) {
                $searchCount = (int) $thisWeekCounts->get($id, 0);
                $previousCount = (int) $lastWeekCounts->get($id, 0);

                return [
                    'rank' => $index + 1,
                    'product' => $products->get($id),
                    'search_count' => $searchCount,
                    'growth_percent' => $previousCount > 0
                        ? (int) round((($searchCount - $previousCount) / $previousCount) * 100)
                        : null,
                ];
            });
    }
}
