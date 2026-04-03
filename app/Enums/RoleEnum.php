<?php

namespace App\Enums;

enum RoleEnum: string
{
    case SUPERADMIN = 'superadmin';
    case MANAGER = 'manager';
    case MEMBER = 'member';

    public function label(): string
    {
        return match ($this) {
            self::SUPERADMIN => 'Super Admin',
            self::MANAGER => 'Manager',
            self::MEMBER => 'Member',
        };
    }
}
