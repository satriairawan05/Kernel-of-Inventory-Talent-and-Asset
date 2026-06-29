<?php

namespace App\Enums;

enum DraftStatusEnum: string
{
    case ACTIVE = 'active';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';

    /**
     * Get the label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
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