<?php

namespace App\Actions\Wishlists;

use App\Models\Wishlist;
use Illuminate\Support\Facades\DB;

class DeleteWishlistAction
{
    public function handle(Wishlist $wishlist): void
    {
        DB::transaction(function () use ($wishlist) {
            $wishlist->delete();
        });
    }
}
