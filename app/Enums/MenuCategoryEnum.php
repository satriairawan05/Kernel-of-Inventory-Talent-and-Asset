<?php

namespace App\Enums;

class MenuCategoryEnum: string
{
    case FOOD = 'food';
    case DRINK = 'drink';
    case SNACK = 'snack';
    case ADDITIONAL = 'additional';

    /**
     * Get all values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get label for each case
     */
    public function label(): string
    {
        return match($this) {
            self::FOOD => 'Food',
            self::DRINK => 'Drinks',
            self::SNACK => 'Snacks',
            self::ADDITIONAL => 'Additional',
        };
    }
}