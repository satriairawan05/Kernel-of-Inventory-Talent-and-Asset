<?php

namespace App\Enums;

class MenuStatusEnum: string
{
    case AVAILABLE = 'available';
    case LOW = 'low';
    case OUT = 'out';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::AVAILABLE => 'Available',
            self::LOW => 'Low Stock',
            self::OUT => 'Out of Stock',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::AVAILABLE => 'bg-success',
            self::LOW => 'bg-warning text-dark',
            self::OUT => 'bg-danger',
        };
    }
}