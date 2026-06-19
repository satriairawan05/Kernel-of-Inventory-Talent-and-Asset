<?php

namespace App\Models;

use App\Models\StockOpnameDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOpnamePeriod extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stock_opname_periods';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'period_start',
        'period_end',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    /**
     * Get the details for the stock opname period.
     */
    public function details(): HasMany
    {
        return $this->hasMany(StockOpnameDetail::class);
    }

    /**
     * Get total product from detail
     */
    public function getTotalProductsAttribute()
    {
        return $this->details?->count();
    }

    /**
     * Get match product from detail
     */
    public function getMatchedProductsAttribute()
    {
        return $this->details?->where('difference', 0)->count();
    }

    /**
     * Get difference product from detail
     */
    public function getDifferenceProductsAttribute()
    {
        return $this->details?->where('difference', '!=', 0)->count();
    }
}
