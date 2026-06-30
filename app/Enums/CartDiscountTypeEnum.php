<?php

namespace App\Enums;

class CartDiscountTypeEnum
{
    const NONE = 'none';
    const RP = 'rp';
    const PERCENT = 'percent';

    public static function values(): array
    {
        return [
            self::NONE,
            self::RP,
            self::PERCENT,
        ];
    }

    public static function labels(): array
    {
        return [
            self::NONE => 'No Discount',
            self::RP => 'Rp',
            self::PERCENT => 'Percent',
        ];
    }

    public static function isValid($value): bool
    {
        return in_array($value, self::values());
    }
}