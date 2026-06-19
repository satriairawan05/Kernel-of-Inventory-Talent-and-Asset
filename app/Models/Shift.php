<?php

namespace App\Models;

use App\Models\Company;
use App\Models\ReportPeriod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shifts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'shift_name',
        'shift_code',
        'start_time',
        'end_time',
        'late_tolerance_minutes',
        'early_leave_tolerance_minutes',
    ];

    /**
     * Get the company that owns the shift.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    /**
     * Relasi One-to-Many ke ReportPeriod.
     * Satu shift dapat memiliki banyak periode laporan.
     *
     * @return HasMany
     */
    public function reportPeriods(): HasMany
    {
        return $this->hasMany(ReportPeriod::class);
    }
}
