<?php

namespace App\Actions\DealOfTheDays;

use App\Models\DealOfTheDay;
use Illuminate\Support\Facades\DB;

class CreateDealOfTheDayAction
{
    /**
     * @param  array{title: string, product_id: int, discount: ?float, discount_type: ?string, status: bool}  $data
     */
    public function handle(array $data): DealOfTheDay
    {
        return DB::transaction(fn () => DealOfTheDay::create($data));
    }
}
