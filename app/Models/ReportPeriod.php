<?php

namespace App\Models;

use App\Models\InventoryReport;
use App\Models\Shift;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportPeriod extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'report_periods';

     /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shift_id',
        'name',
        'is_active',
    ];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ==================== RELASI ====================

    /**
     * Relasi Many-to-One ke model Shift.
     * Periode ini terkait dengan satu shift (misal: Pagi, Siang, Malam).
     *
     * @return BelongsTo
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * Relasi One-to-Many ke model InventoryReport.
     * Satu periode dapat memiliki banyak laporan inventory.
     *
     * @return HasMany
     */
    public function inventoryReports(): HasMany
    {
        return $this->hasMany(InventoryReport::class);
    }

    // ==================== AKSESOR ====================

    /**
     * Aksesor untuk mendapatkan waktu mulai shift dari relasi.
     * Jika shift tidak ada, mengembalikan null.
     *
     * @return string|null
     */
    public function getStartTimeAttribute(): ?string
    {
        return $this->shift?->start_time;
    }

    /**
     * Aksesor untuk mendapatkan waktu selesai shift dari relasi.
     * Jika shift tidak ada, mengembalikan null.
     *
     * @return string|null
     */
    public function getEndTimeAttribute(): ?string
    {
        return $this->shift?->end_time;
    }
}
