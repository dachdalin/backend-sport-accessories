<?php

namespace App\Actions\Wishlists;

use App\Models\Wishlist;
use Illuminate\Support\Facades\DB;

class CreateWishlistAction
{
    /**
     * @param  array{product_id: int, customer_name: string, customer_email: ?string}  $data
     */
    public function handle(array $data): Wishlist
    {
        return DB::transaction(function () use ($data) {
            return Wishlist::create($data);
        });
    }
}
