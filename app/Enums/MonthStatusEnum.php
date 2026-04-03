<?php

namespace App\Enums;

enum MonthStatusEnum: string
{
    case ACTIVE = 'active';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::CLOSED => 'Closed',
        };
    }
}
