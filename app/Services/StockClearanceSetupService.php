<?php

namespace App\Services;

use App\Models\StockClearanceSetup;
use Illuminate\Database\Eloquent\Collection;

class StockClearanceSetupService
{
    /**
     * List all stock clearance setups with their item counts, most recently created first.
     *
     * @return Collection<int, StockClearanceSetup>
     */
    public function list(): Collection
    {
        return StockClearanceSetup::query()
            ->withCount('items')
            ->latest()
            ->get();
    }
}
