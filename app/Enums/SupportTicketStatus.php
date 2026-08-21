<?php

namespace App\Enums;

enum SupportTicketStatus: string
{
    case Open = 'open';
    case Answered = 'answered';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::Answered => 'Answered',
            self::Closed => 'Closed',
        };
    }
}
