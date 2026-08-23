<?php

namespace App\Services;

use App\Models\StockClearanceSetup;
use Illuminate\Pagination\LengthAwarePaginator;

class StockClearanceSetupService
{
    /**
     * List stock clearance setups with their item counts, most recently created first.
     */
    public function list(): LengthAwarePaginator
    {
        return StockClearanceSetup::query()
            ->withCount('items')
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }
}
