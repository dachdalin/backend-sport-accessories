<?php

namespace App\Actions\ProductSearches;

use App\Models\ProductSearch;
use Illuminate\Support\Facades\DB;

class LogProductSearchAction
{
    /**
     * Record one search-result event per product ID, all timestamped now.
     *
     * @param  array<int, int>  $productIds
     */
    public function handle(array $productIds): void
    {
        if ($productIds === []) {
            return;
        }

        $now = now();

        DB::transaction(function () use ($productIds, $now) {
            ProductSearch::query()->insert(array_map(
                fn (int $productId) => ['product_id' => $productId, 'created_at' => $now],
                $productIds,
            ));
        });
    }
}
