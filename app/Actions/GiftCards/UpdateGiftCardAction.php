<?php

namespace App\Actions\GiftCards;

use App\Models\GiftCard;
use Illuminate\Support\Facades\DB;

class UpdateGiftCardAction
{
    /**
     * @param  array{code: string, balance: float, expires_at: ?string, status: bool}  $data
     */
    public function handle(GiftCard $giftCard, array $data): GiftCard
    {
        return DB::transaction(function () use ($giftCard, $data) {
            $giftCard->update($data);

            return $giftCard;
        });
    }
}
