<?php

namespace App\Actions\LoyaltyTiers;

use App\Models\LoyaltyTier;
use Illuminate\Support\Facades\DB;

class CreateLoyaltyTierAction
{
    /**
     * @param  array{name: string, points_required: int, discount_percentage: int, status: bool}  $data
     */
    public function handle(array $data): LoyaltyTier
    {
        return DB::transaction(fn () => LoyaltyTier::create($data));
    }
}
