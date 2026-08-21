<?php

namespace App\Actions\StockClearanceSetups;

use App\Models\StockClearanceSetup;
use Illuminate\Support\Facades\DB;

class CreateStockClearanceSetupAction
{
    /**
     * @param  array{discount_type: string, discount_amount: float, offer_active_time: string, offer_active_range_start: ?string, offer_active_range_end: ?string, show_in_homepage: bool, show_in_homepage_once: bool, show_in_shop: bool, is_active: bool, duration_start_date: string, duration_end_date: string}  $data
     * @param  array<int, array{product_id: int, discount_type: string, discount_amount: float, is_active: bool}>  $items
     */
    public function handle(array $data, array $items): StockClearanceSetup
    {
        return DB::transaction(function () use ($data, $items) {
            $setup = StockClearanceSetup::create($data);

            $setup->items()->createMany($items);

            return $setup;
        });
    }
}
