<?php

namespace App\Models;

use App\Models\Shift;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportPeriod extends Model
{
    protected $table = 'report_periods';

    protected $fillable = [
        'company_id',
        'shift_id',
        'date',
        'name',
        'is_active',
    ];

    protected $casts = [
        'date'      => 'date',
        'is_active' => 'boolean',
    ];

    // ==================== RELASI ====================

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function inventoryReports(): HasMany
    {
        return $this->hasMany(InventoryReport::class);
    }

    // ==================== AKSESOR ====================

    /**
     * Otomatis generate nama periode dari nama shift dan tanggal.
     */
    public function getNameAttribute($value): string
    {
        if ($value) {
            return $value;
        }

        // Jika name null, buat dari shift dan date
        $shiftName = $this->shift?->name ?? 'Shift';
        $date = $this->date?->format('Y-m-d') ?? now()->toDateString();
        return "{$shiftName} - {$date}";
    }

    /**
     * Waktu mulai shift (dari relasi shift).
     */
    public function getStartTimeAttribute(): ?string
    {
        return $this->shift?->start_time;
    }

    /**
     * Waktu selesai shift (dari relasi shift).
     */
    public function getEndTimeAttribute(): ?string
    {
        return $this->shift?->end_time;
    }

    // ==================== HELPER ====================

    /**
     * Mendapatkan atau membuat ReportPeriod untuk hari ini,
     * berdasarkan shift yang aktif pada waktu sekarang.
     */
    public static function getActivePeriod(int $companyId): self
    {
        // 1. Cari shift yang aktif saat ini (berdasarkan waktu sekarang)
        $now = now()->format('H:i:s');
        $shift = Shift::where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->first();

        // Jika tidak ada shift aktif, ambil shift pertama atau default
        if (!$shift) {
            $shift = Shift::first();
        }

        // 2. Cari atau buat ReportPeriod untuk hari ini, shift tersebut, dan company
        $today = now()->toDateString();
        $period = self::firstOrCreate(
            [
                'company_id' => $companyId,
                'shift_id'   => $shift->id,
                'date'       => $today,
            ],
            [
                'is_active' => true,
                // name akan di-generate otomatis oleh accessor
            ]
        );

        return $period;
    }
}