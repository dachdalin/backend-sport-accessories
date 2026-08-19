<?php

namespace App\Actions\Currencies;

use App\Models\Currency;

class DeleteCurrencyAction
{
    public function handle(Currency $currency): void
    {
        $currency->delete();
    }
}
