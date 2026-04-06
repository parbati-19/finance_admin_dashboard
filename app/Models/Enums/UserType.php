<?php

namespace App\Models\Enums;

enum UserType: int
{
    case Individual = 0;
    case Company = 1;

    public function label(): string
    {
        return match ($this) {
            self::Individual => 'Individual',
            self::Company => 'Company',
        };
    }
}
