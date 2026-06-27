<?php

namespace App\Enums;

class CashSummaryTypeEnum: string
{
    case CASH_IN = 'Cash In';
    case CASH_OUT = 'Cash Out';

    public function label(): string
    {
        return match ($this) {
            self::CASH_IN  => 'Cash In',
            self::CASH_OUT => 'Cash Out',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }
}