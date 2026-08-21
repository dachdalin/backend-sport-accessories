<?php

namespace App\Actions\StockClearanceSetups;

use App\Models\StockClearanceSetup;
use Illuminate\Support\Facades\DB;

class DeleteStockClearanceSetupAction
{
    public function handle(StockClearanceSetup $stockClearanceSetup): void
    {
        DB::transaction(function () use ($stockClearanceSetup) {
            $stockClearanceSetup->delete();
        });
    }
}
