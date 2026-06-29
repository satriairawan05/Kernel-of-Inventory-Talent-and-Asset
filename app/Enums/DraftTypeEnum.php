<?php

namespace App\Enums;

enum DraftTypeEnum: string
{
    case DINEIN = 'dinein';
    case TAKEAWAY = 'takeaway';

    /**
     * Get the label for the draft type.
     */
    public function label(): string
    {
        return match ($this) {
            self::DINEIN => 'Dine In',
            self::TAKEAWAY => 'Take Away',
        };
    }

    /**
     * Get all values as array.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}