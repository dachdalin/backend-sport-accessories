<?php

namespace App\Services;

use App\Models\FlashDeal;
use Illuminate\Database\Eloquent\Collection;

class FlashDealService
{
    /**
     * List all flash deals with their items, most recently created first.
     *
     * @return Collection<int, FlashDeal>
     */
    public function list(): Collection
    {
        return FlashDeal::query()
            ->withCount('items')
            ->latest()
            ->get();
    }
}
