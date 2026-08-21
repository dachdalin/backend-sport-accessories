<?php

namespace App\Actions\DealOfTheDays;

use App\Models\DealOfTheDay;
use Illuminate\Support\Facades\DB;

class UpdateDealOfTheDayAction
{
    /**
     * @param  array{title: string, product_id: int, discount: ?float, discount_type: ?string, status: bool}  $data
     */
    public function handle(DealOfTheDay $dealOfTheDay, array $data): DealOfTheDay
    {
        DB::transaction(function () use ($dealOfTheDay, $data) {
            $dealOfTheDay->update($data);
        });

        return $dealOfTheDay;
    }
}
