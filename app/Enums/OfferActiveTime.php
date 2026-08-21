<?php

namespace App\Enums;

enum OfferActiveTime: string
{
    case Always = 'always';
    case SpecificTime = 'specific_time';

    public function label(): string
    {
        return match ($this) {
            self::Always => 'Always',
            self::SpecificTime => 'Specific time range',
        };
    }
}
