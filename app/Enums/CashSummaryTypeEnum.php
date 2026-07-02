<?php

namespace App\Enums;

enum CashSummaryTypeEnum: string
{
    case CASH_IN = 'cash_in';
    case CASH_OUT = 'cash_out';
    case SALES = 'sales';
    case DISCOUNT = 'discount';
    case ADJUSTMENT = 'adjustment';

    public function label(): string
    {
        return match ($this) {
            self::CASH_IN => 'Cash In',
            self::CASH_OUT => 'Cash Out',
            self::SALES => 'Sales',
            self::DISCOUNT => 'Discount',
            self::ADJUSTMENT => 'Adjustment',
        };
    }
}