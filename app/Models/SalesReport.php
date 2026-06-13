<?php

namespace App\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesReport extends Model
{
    // Define the table name used by this model
    protected $table = 'sales_reports';

    // Allow mass assignment for these fields
    protected $fillable = [
        'company_id',
        'report_date',
        'arrived_date',
        'pulsa_amount',
        'accessories_amount',
        'service_amount',
        'total_amount',
        'notes',
    ];

    /**
     * Get the company that owns the sales report.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
