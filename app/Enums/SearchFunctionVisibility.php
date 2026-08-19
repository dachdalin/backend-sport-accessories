<?php

namespace App\Enums;

enum SearchFunctionVisibility: string
{
    case Admin = 'admin';
    case Seller = 'seller';
    case Customer = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Seller => 'Seller',
            self::Customer => 'Customer',
        };
    }
}
