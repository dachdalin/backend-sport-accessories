<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ListTrendingProductsRequest;
use App\Http\Resources\Api\V1\ProductResource;
use App\Services\TrendingProductService;
use Illuminate\Http\JsonResponse;

class TrendingController extends Controller
{
    /**
     * The most-searched active products over the last 7 days, most searched first.
     *
     * Query params: `limit` (default 10, max 50).
     */
    public function index(ListTrendingProductsRequest $request, TrendingProductService $trendingProductService): JsonResponse
    {
        $trending = $trendingProductService->trending($request->integer('limit', 10));

        return response()->json([
            'data' => $trending->map(fn (array $entry) => [
                'rank' => $entry['rank'],
                'product' => new ProductResource($entry['product']),
                'search_count' => $entry['search_count'],
                'growth_percent' => $entry['growth_percent'],
            ])->values(),
        ]);
    }
}
