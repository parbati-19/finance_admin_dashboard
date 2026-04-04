<?php

namespace App\Models\Enums;

enum TransactionType: int
{
    case Income = 1;
    case Expense = 2;

    public function label(): string
    {
        return match ($this) {
            self::Income => 'Income',
            self::Expense => 'Expense',
        };
    }
}