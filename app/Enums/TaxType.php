<?php

namespace App\Enums;

enum TaxType: string
{
    case Percent = 'percent';
    case Amount = 'amount';

    public function label(): string
    {
        return match ($this) {
            self::Percent => 'Percentage',
            self::Amount => 'Fixed amount',
        };
    }
}
