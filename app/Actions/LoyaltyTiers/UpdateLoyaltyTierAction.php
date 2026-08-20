<?php

namespace App\Actions\LoyaltyTiers;

use App\Models\LoyaltyTier;
use Illuminate\Support\Facades\DB;

class UpdateLoyaltyTierAction
{
    /**
     * @param  array{name: string, points_required: int, discount_percentage: int, status: bool}  $data
     */
    public function handle(LoyaltyTier $loyaltyTier, array $data): LoyaltyTier
    {
        DB::transaction(function () use ($loyaltyTier, $data) {
            $loyaltyTier->update($data);
        });

        return $loyaltyTier;
    }
}
