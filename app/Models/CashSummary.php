<?php

namespace App\Models;

use App\Enums\CashSummaryTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CashSummary Model
 *
 * Represents a cash transaction record (cash in or cash out) for a specific company.
 *
 * @property int $id
 * @property int $company_id
 * @property string $type  (cash_in or cash_out)
 * @property int $amount   (in rupiah)
 * @property string|null $description
 * @property string $transaction_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Company $company
 */
class CashSummary extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cash_summaries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'type',
        'amount',
        'description',
        'transaction_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount'           => 'integer',
        'transaction_date' => 'date',
        'type'             => CashSummaryTypeEnum::class,
    ];

    // ========================================================
    // RELATIONS
    // ========================================================

    /**
     * Get the company that owns this cash summary.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // ========================================================
    // SCOPES
    // ========================================================

    /**
     * Scope a query to only include cash_in records.
     */
    public function scopeCashIn($query)
    {
        return $query->where('type', CashSummaryTypeEnum::CASH_IN->value);
    }

    /**
     * Scope a query to only include cash_out records.
     */
    public function scopeCashOut($query)
    {
        return $query->where('type', CashSummaryTypeEnum::CASH_OUT->value);
    }

    /**
     * Scope a query to filter by company ID.
     */
    public function scopeByCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('transaction_date', [$start, $end]);
    }

    // ========================================================
    // ACCESSORS
    // ========================================================

    /**
     * Get the formatted amount with currency symbol.
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Get the human-readable label for the type.
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->type?->label() ?? $this->type;
    }

    // ========================================================
    // MUTATORS
    // ========================================================

    /**
     * Mutate amount to ensure it is stored as integer.
     */
    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = (int) $value;
    }

    // ========================================================
    // HELPERS
    // ========================================================

    /**
     * Calculate the net balance (cash_in - cash_out) for a given company.
     *
     * @param int $companyId
     * @param string|null $start  (optional start date)
     * @param string|null $end    (optional end date)
     * @return int
     */
    public static function getBalance(int $companyId, ?string $start = null, ?string $end = null): int
    {
        $query = self::byCompany($companyId);
        if ($start && $end) {
            $query->betweenDates($start, $end);
        }

        $totalIn = (clone $query)->cashIn()->sum('amount');
        $totalOut = (clone $query)->cashOut()->sum('amount');

        return $totalIn - $totalOut;
    }

    /**
     * Get the current balance for this specific cash summary's company.
     * (Convenience method if you have an instance.)
     */
    public function getCurrentBalanceAttribute(): int
    {
        return self::getBalance($this->company_id);
    }
}