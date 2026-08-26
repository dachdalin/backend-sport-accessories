<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\FlashDealResource;
use App\Models\FlashDeal;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FlashDealController extends Controller
{
    /**
     * Display a paginated listing of the currently running flash deals.
     */
    public function index(): AnonymousResourceCollection
    {
        return FlashDealResource::collection(
            FlashDeal::query()
                ->where('status', true)
                ->whereDate('start_date', '<=', today())
                ->whereDate('end_date', '>=', today())
                ->withCount('items')
                ->latest()
                ->paginate(15)
                ->withQueryString(),
        );
    }

    /**
     * Display the specified flash deal with its discounted products.
     */
    public function show(FlashDeal $flashDeal): FlashDealResource
    {
        abort_unless($this->isRunning($flashDeal), 404);

        return new FlashDealResource(
            $flashDeal->load(['items.product.category', 'items.product.brand']),
        );
    }

    /**
     * Determine whether the given flash deal is active and within its date range.
     */
    private function isRunning(FlashDeal $flashDeal): bool
    {
        return $flashDeal->status
            && ! today()->lt($flashDeal->start_date)
            && ! today()->gt($flashDeal->end_date);
    }
}
