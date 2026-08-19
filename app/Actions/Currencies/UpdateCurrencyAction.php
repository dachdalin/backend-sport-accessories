<?php

namespace App\Actions\Currencies;

use App\Models\Currency;

class UpdateCurrencyAction
{
    /**
     * @param  array{name: string, symbol: string, code: string, exchange_rate: float, status?: bool}  $data
     */
    public function handle(Currency $currency, array $data): Currency
    {
        $currency->update($data);

        return $currency;
    }
}
