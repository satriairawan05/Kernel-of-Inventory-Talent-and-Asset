<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cart_id',
        'draft_id',
        'user_id',
        'company_id',
        'transaction_number',
        'transaction_date',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'total',
        'payment_method',
        'paid',
        'change',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'transaction_date' => 'datetime',
        'subtotal'          => 'integer',
        'discount_value'    => 'integer',
        'discount_amount'   => 'integer',
        'total'             => 'integer',
        'paid'              => 'integer',
        'change'            => 'integer',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array<int, string>
     */
    protected $dates = [
        'transaction_date',
    ];

    // ============================================================
    // RELATIONS
    // ============================================================

    /**
     * Get the cart that owns the transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the draft that owns the transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function draft(): BelongsTo
    {
        return $this->belongsTo(Draft::class);
    }

    /**
     * Get the user that owns the transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company that owns the transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the items for the transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    // ============================================================
    // HELPERS
    // ============================================================

    /**
     * Generate a unique transaction number with daily reset.
     *
     * Format: KITA/YYYY/MM/DD/XXXX (4 digits, reset at midnight).
     *
     * @return string
     */
    public static function generateTransactionNumber(): string
    {
        $date = now()->format('Ymd');
        $key  = "trx_counter_{$date}";

        $counter = Cache::get($key, 0);
        $counter++;

        // Set expiry until end of day
        Cache::put($key, $counter, now()->endOfDay());

        $year   = now()->year;
        $month  = now()->month;
        $day    = now()->day;
        $padded = str_pad($counter, 4, '0', STR_PAD_LEFT);

        return "KITA/{$year}/{$month}/{$day}/{$padded}";
    }

    // ============================================================
    // SCOPES
    // ============================================================

    /**
     * Scope a query to filter transactions by date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \DateTimeInterface|string  $from
     * @param  \DateTimeInterface|string  $to
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateBetween($query, $from, $to)
    {
        return $query->whereBetween('transaction_date', [$from, $to]);
    }

    /**
     * Scope a query to filter transactions by company ID.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $companyId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope a query to filter transactions by user ID.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ============================================================
    // ACCESSORS
    // ============================================================

    /**
     * Get the transaction date in Indonesian format.
     *
     * @return string
     */
    public function getFormattedDateAttribute(): string
    {
        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $date = $this->transaction_date ?: $this->created_at;
        return $date->day . ' ' . $months[$date->month - 1] . ' ' . $date->year . ' ' . $date->format('H:i');
    }
}