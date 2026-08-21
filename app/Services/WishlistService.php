<?php

namespace App\Services;

use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Collection;

class WishlistService
{
    /**
     * List all wishlist entries with their product, most recently added first.
     *
     * @return Collection<int, Wishlist>
     */
    public function list(): Collection
    {
        return Wishlist::query()
            ->with('product:id,name')
            ->latest()
            ->get();
    }
}
