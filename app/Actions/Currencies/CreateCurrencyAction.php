<?php

namespace App\Actions\Currencies;

use App\Models\Currency;

class CreateCurrencyAction
{
    /**
     * @param  array{name: string, symbol: string, code: string, exchange_rate: float, status?: bool}  $data
     */
    public function handle(array $data): Currency
    {
        return Currency::create($data);
    }
}
