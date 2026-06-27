<?php

namespace App\Enums;

enum CashSummaryTypeEnum: string
{
    case CASH_IN = 'Cash In';
    case CASH_OUT = 'Cash Out';

    /**
     * Get the human-readable label for the enum case.
     */
    public function label(): string
    {
        return match ($this) {
            self::CASH_IN  => 'Cash In',
            self::CASH_OUT => 'Cash Out',
        };
    }

    /**
     * Get all enum values as an array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get key-value pairs for dropdown options.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }
}