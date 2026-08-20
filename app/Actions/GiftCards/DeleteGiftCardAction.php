<?php

namespace App\Actions\GiftCards;

use App\Models\GiftCard;
use Illuminate\Support\Facades\DB;

class DeleteGiftCardAction
{
    public function handle(GiftCard $giftCard): void
    {
        DB::transaction(function () use ($giftCard) {
            $giftCard->delete();
        });
    }
}
