<?php

namespace App\Enums;

/**
 * Enum untuk status sesi kasir.
 * Hanya ada 2 status: OPEN (aktif) dan CLOSED (ditutup).
 */
enum CashierSessionStatusEnum: string
{
    case OPEN = 'open';
    case CLOSED = 'closed';

    /**
     * Get Label for UI
     */
    public function label(): string
    {
        return match ($this) {
            self::OPEN   => 'OPEN',
            self::CLOSED => 'CLOSED',
        };
    }

    /**
     * Get Badge Class for UI
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::OPEN   => 'bg-success',
            self::CLOSED => 'bg-secondary',
        };
    }
}