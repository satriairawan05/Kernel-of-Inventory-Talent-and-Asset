<?php

namespace App\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shift extends Model
{
    protected $table = 'shifts';

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
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(
            Company::class,
            'company_id',
            'id'
        );
    }
}
