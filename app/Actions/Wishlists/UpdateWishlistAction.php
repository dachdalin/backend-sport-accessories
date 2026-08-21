<?php

namespace App\Actions\Wishlists;

use App\Models\Wishlist;
use Illuminate\Support\Facades\DB;

class UpdateWishlistAction
{
    /**
     * @param  array{product_id: int, customer_name: string, customer_email: ?string}  $data
     */
    public function handle(Wishlist $wishlist, array $data): Wishlist
    {
        return DB::transaction(function () use ($wishlist, $data) {
            $wishlist->update($data);

            return $wishlist;
        });
    }
}
