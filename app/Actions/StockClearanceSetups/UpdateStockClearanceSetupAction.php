<?php

namespace App\Actions\StockClearanceSetups;

use App\Models\StockClearanceSetup;
use Illuminate\Support\Facades\DB;

class UpdateStockClearanceSetupAction
{
    /**
     * @param  array{discount_type: string, discount_amount: float, offer_active_time: string, offer_active_range_start: ?string, offer_active_range_end: ?string, show_in_homepage: bool, show_in_homepage_once: bool, show_in_shop: bool, is_active: bool, duration_start_date: string, duration_end_date: string}  $data
     * @param  array<int, array{product_id: int, discount_type: string, discount_amount: float, is_active: bool}>  $items
     */
    public function handle(StockClearanceSetup $stockClearanceSetup, array $data, array $items): StockClearanceSetup
    {
        return DB::transaction(function () use ($stockClearanceSetup, $data, $items) {
            $stockClearanceSetup->update($data);

            $stockClearanceSetup->items()->delete();
            $stockClearanceSetup->items()->createMany($items);

            return $stockClearanceSetup;
        });
    }
}
