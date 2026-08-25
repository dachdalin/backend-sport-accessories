<?php

namespace App\Services;

use App\Models\Wishlist;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WishlistService
{
    /**
     * List all wishlist entries with their product, most recently added first.
     */
    public function list(): LengthAwarePaginator
    {
        return Wishlist::query()
            ->with('product:id,name')
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }
}
