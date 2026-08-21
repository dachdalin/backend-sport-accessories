<?php

namespace App\Actions\DealOfTheDays;

use App\Models\DealOfTheDay;
use Illuminate\Support\Facades\DB;

class DeleteDealOfTheDayAction
{
    public function handle(DealOfTheDay $dealOfTheDay): void
    {
        DB::transaction(function () use ($dealOfTheDay) {
            $dealOfTheDay->delete();
        });
    }
}
