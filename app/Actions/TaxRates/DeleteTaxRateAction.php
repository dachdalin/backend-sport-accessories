<?php

namespace App\Actions\TaxRates;

use App\Models\TaxRate;
use Illuminate\Support\Facades\DB;

class DeleteTaxRateAction
{
    public function handle(TaxRate $taxRate): void
    {
        DB::transaction(function () use ($taxRate) {
            $taxRate->delete();
        });
    }
}
