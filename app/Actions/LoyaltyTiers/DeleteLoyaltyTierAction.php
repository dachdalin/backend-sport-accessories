<?php

namespace App\Actions\LoyaltyTiers;

use App\Models\LoyaltyTier;
use Illuminate\Support\Facades\DB;

class DeleteLoyaltyTierAction
{
    public function handle(LoyaltyTier $loyaltyTier): void
    {
        DB::transaction(function () use ($loyaltyTier) {
            $loyaltyTier->delete();
        });
    }
}
