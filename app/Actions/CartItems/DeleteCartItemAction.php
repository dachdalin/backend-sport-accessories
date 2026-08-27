<?php

namespace App\Actions\CartItems;

use App\Models\CartItem;
use Illuminate\Support\Facades\DB;

class DeleteCartItemAction
{
    public function handle(CartItem $cartItem): void
    {
        DB::transaction(function () use ($cartItem) {
            $cartItem->delete();
        });
    }
}
