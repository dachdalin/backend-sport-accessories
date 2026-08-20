<?php

namespace App\Enums;

enum CouponType: string
{
    case Fixed = 'fixed';
    case Percentage = 'percentage';

    public function label(): string
    {
        return match ($this) {
            self::Fixed => 'Fixed amount',
            self::Percentage => 'Percentage',
        };
    }
}
