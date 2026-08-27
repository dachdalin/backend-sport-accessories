<?php

namespace App\Actions\CartItems;

use App\Models\CartItem;
use Illuminate\Support\Facades\DB;

class CreateCartItemAction
{
    /**
     * @param  array{customer_id: int, product_id: int, quantity: int}  $data
     */
    public function handle(array $data): CartItem
    {
        return DB::transaction(function () use ($data) {
            $cartItem = CartItem::query()
                ->where('customer_id', $data['customer_id'])
                ->where('product_id', $data['product_id'])
                ->first();

            if ($cartItem) {
                $cartItem->increment('quantity', $data['quantity']);

                return $cartItem;
            }

            return CartItem::create($data);
        });
    }
}
