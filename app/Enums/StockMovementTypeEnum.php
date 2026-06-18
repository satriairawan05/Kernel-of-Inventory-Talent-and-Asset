<?php

namespace App\Enums;

class StockMovementTypeEnum
{
    public const OPENING = 'opening';
    public const PURCHASE = 'purchase';
    public const SALE = 'sale';
    public const ADJUSTMENT = 'adjustment';
    public const OPNAME = 'opname';
    public const RETURN = 'return';
    public const TRANSFER = 'transfer';

    public static function labels(): array
    {
        return [
            self::OPENING => 'Opening',
            self::PURCHASE => 'Purchase',
            self::SALE => 'Sale',
            self::ADJUSTMENT => 'Adjustment',
            self::OPNAME => 'Opname',
            self::RETURN => 'Return',
            self::TRANSFER => 'Transfer',
        ];
    }

    public static function values(): array
    {
        return array_keys(self::labels());
    }

    public static function label(string $value): string
    {
        return self::labels()[$value] ?? $value;
    }
}
