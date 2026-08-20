<?php

namespace App\Actions\TaxRates;

use App\Models\TaxRate;
use Illuminate\Support\Facades\DB;

class UpdateTaxRateAction
{
    /**
     * @param  array{name: string, region: ?string, rate: float, is_default?: bool, status: bool}  $data
     */
    public function handle(TaxRate $taxRate, array $data): TaxRate
    {
        return DB::transaction(function () use ($taxRate, $data) {
            if ($data['is_default'] ?? false) {
                TaxRate::query()->where('id', '!=', $taxRate->id)->where('is_default', true)->update(['is_default' => false]);
            }

            $taxRate->update($data);

            return $taxRate;
        });
    }
}
