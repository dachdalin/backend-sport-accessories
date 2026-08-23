<?php

namespace App\Services;

use App\Models\FlashDeal;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FlashDealService
{
    /**
     * Paginate flash deals with their item count, most recently created first.
     */
    public function list(): LengthAwarePaginator
    {
        return FlashDeal::query()
            ->withCount('items')
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }
}
