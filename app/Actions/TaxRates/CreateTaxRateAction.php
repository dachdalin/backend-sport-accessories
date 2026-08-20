<?php

namespace App\Actions\TaxRates;

use App\Models\TaxRate;
use Illuminate\Support\Facades\DB;

class CreateTaxRateAction
{
    /**
     * @param  array{name: string, region: ?string, rate: float, is_default?: bool, status: bool}  $data
     */
    public function handle(array $data): TaxRate
    {
        return DB::transaction(function () use ($data) {
            if ($data['is_default'] ?? false) {
                TaxRate::query()->where('is_default', true)->update(['is_default' => false]);
            }

            return TaxRate::create($data);
        });
    }
}
