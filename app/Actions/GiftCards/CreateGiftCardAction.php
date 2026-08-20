<?php

namespace App\Actions\GiftCards;

use App\Models\GiftCard;
use Illuminate\Support\Facades\DB;

class CreateGiftCardAction
{
    /**
     * @param  array{code: string, initial_balance: float, expires_at: ?string, status: bool}  $data
     */
    public function handle(array $data): GiftCard
    {
        $data['balance'] = $data['initial_balance'];

        return DB::transaction(fn () => GiftCard::create($data));
    }
}
