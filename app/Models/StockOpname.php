<?php

namespace App\Models;

use App\Models\ProductVariant;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpname extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stock_opnames';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_variant_id',
        'system_stock',
        'physical_stock',
        'difference',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'system_stock' => 'decimal:2',
        'physical_stock' => 'decimal:2',
        'difference' => 'decimal:2',
    ];

     /**
     * Get the product variant that owns the stock opname.
     */
    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Get detailed movements within this opname period.
     */
    public function movements()
    {
        return StockMovement::whereBetween('created_at', [$this->period_start, $this->period_end])
            ->orderBy('created_at');
    }

    /**
     * Get summary per product variant for this period.
     */
    public function getVariantSummaryAttribute()
    {
        // This will be calculated on demand
        return $this->calculateVariantSummary();
    }

    public function calculateVariantSummary()
    {
        // Get all movements in period
        $movements = $this->movements()->with('productVariant.product')->get();

        // Group by variant
        $groups = $movements->groupBy('product_variant_id');

        $summary = [];
        foreach ($groups as $variantId => $items) {
            $variant = $items->first()->productVariant;
            $productName = $variant->product->product_name ?? 'Unknown';
            $variantName = $variant->variant_name ?? '';

            // Calculate total in and out
            $totalIn = $items->where('qty', '>', 0)->sum('qty');
            $totalOut = $items->where('qty', '<', 0)->sum('qty') * -1; // absolute

            // Calculate starting stock: stock before period start
            $startStock = $this->getStockBeforePeriod($variantId);
            $endStock = $startStock + $totalIn - $totalOut;

            $summary[] = (object) [
                'variant_id' => $variantId,
                'product_name' => $productName,
                'variant_name' => $variantName,
                'start_stock' => $startStock,
                'total_in' => $totalIn,
                'total_out' => $totalOut,
                'end_stock' => $endStock,
            ];
        }

        return collect($summary);
    }

    private function getStockBeforePeriod($variantId)
    {
        // Get the last movement before period_start for this variant
        $lastMovement = StockMovement::where('product_variant_id', $variantId)
            ->where('created_at', '<', $this->period_start)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastMovement) {
            return $lastMovement->stock_after;
        }

        // If no movement, check stock table for initial stock
        $stock = Stock::where('product_variant_id', $variantId)->first();
        return $stock ? $stock->current_stock : 0; // This might not be accurate if stock was updated after period start, but fallback.
    }
}
