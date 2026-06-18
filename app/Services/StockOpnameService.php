<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockOpname;
use App\Models\StockMovement;
use App\Enums\StockMovementTypeEnum;
use Illuminate\Support\Facades\DB;

class StockOpnameService
{
    /**
     * Calculate total stock before a given date (sum of all current stocks at that moment).
     * This is simplified: we sum stock_after from the last movement before date for each variant.
     */
    private function getTotalStockBefore(Carbon $date): float
    {
        // Get all variant IDs that have stock
        $variantIds = StockMovement::distinct('product_variant_id')->pluck('product_variant_id');

        $total = 0;
        foreach ($variantIds as $vid) {
            $lastMovement = StockMovement::where('product_variant_id', $vid)
                ->where('created_at', '<', $date)
                ->orderBy('created_at', 'desc')
                ->first();
            if ($lastMovement) {
                $total += $lastMovement->stock_after;
            } else {
                // Fallback to stock table
                $stock = Stock::where('product_variant_id', $vid)->first();
                $total += $stock ? $stock->current_stock : 0;
            }
        }
        return $total;
    }

        /**
     * Create a new stock opname and adjust stock if difference exists.
     *
     * @param array $data
     * @return \App\Models\StockOpname
     */
    public function store(array $data): StockOpname
    {
        return DB::transaction(function () use ($data) {
            $variantId = $data['product_variant_id'];
            $physicalStock = $data['physical_stock'];

            // Get system stock with lock for update (avoid race condition)
            $stock = Stock::where('product_variant_id', $variantId)->lockForUpdate()->first();

            if (!$stock) {
                $stock = Stock::create([
                    'product_variant_id' => $variantId,
                    'current_stock'      => 0,
                ]);
            }

            $systemStock = $stock->current_stock;
            $difference = $physicalStock - $systemStock;

            // Create opname record
            $opname = StockOpname::create([
                'product_variant_id' => $variantId,
                'system_stock'       => $systemStock,
                'physical_stock'     => $physicalStock,
                'difference'         => $difference,
                'notes'              => $data['notes'] ?? null,
            ]);

            // If difference exists, adjust stock and create movement
            if (abs($difference) > 0.00001) {
                $stockBefore = $systemStock;
                $stockAfter = $physicalStock;

                $stock->update(['current_stock' => $physicalStock]);

                StockMovement::create([
                    'product_variant_id' => $variantId,
                    'movement_type'      => StockMovementTypeEnum::OPNAME,
                    'qty'                => $difference,
                    'stock_before'       => $stockBefore,
                    'stock_after'        => $stockAfter,
                    'notes'              => 'Penyesuaian opname: ' . ($data['notes'] ?? ''),
                    'user_id'            => auth()->user()->id,
                ]);
            }

            return $opname;
        });
    }

    /**
     * Update an existing opname and re-adjust stock.
     *
     * @param \App\Models\StockOpname $opname
     * @param array $data
     * @return \App\Models\StockOpname
     */
    public function update(StockOpname $opname, array $data): StockOpname
    {
        return DB::transaction(function () use ($opname, $data) {
            $variantId = $data['product_variant_id'];
            $newPhysical = $data['physical_stock'];

            // Get current stock with lock
            $stock = Stock::where('product_variant_id', $variantId)->lockForUpdate()->firstOrFail();

            // Rollback previous opname effect
            $oldDiff = $opname->difference;
            $stock->current_stock -= $oldDiff; // revert to before opname

            // Calculate new difference
            $systemStock = $stock->current_stock;
            $newDiff = $newPhysical - $systemStock;

            // Update opname record
            $opname->update([
                'physical_stock' => $newPhysical,
                'difference'     => $newDiff,
                'notes'          => $data['notes'] ?? null,
            ]);

            // Remove old movement if exists (only one movement per opname)
            StockMovement::where('product_variant_id', $variantId)
                ->where('movement_type', StockMovementTypeEnum::OPNAME)
                ->where('notes', 'like', 'Penyesuaian opname%')
                ->where('created_at', '>=', $opname->created_at->subSeconds(5))
                ->delete();

            // Adjust stock if new difference exists
            if (abs($newDiff) > 0.00001) {
                $stockBefore = $systemStock;
                $stockAfter = $newPhysical;

                $stock->update(['current_stock' => $newPhysical]);

                StockMovement::create([
                    'product_variant_id' => $variantId,
                    'movement_type'      => StockMovementTypeEnum::OPNAME,
                    'qty'                => $newDiff,
                    'stock_before'       => $stockBefore,
                    'stock_after'        => $stockAfter,
                    'notes'              => 'Penyesuaian opname: ' . ($data['notes'] ?? ''),
                    'user_id'            => auth()->user()->id,
                ]);
            } else {
                // If no difference, ensure stock is set to system stock (unchanged)
                $stock->update(['current_stock' => $systemStock]);
            }

            return $opname->fresh();
        });
    }

    /**
     * Delete an opname and revert stock to before opname.
     *
     * @param \App\Models\StockOpname $opname
     * @return bool
     */
    public function destroy(StockOpname $opname): bool
    {
        return DB::transaction(function () use ($opname) {
            $variantId = $opname->product_variant_id;
            $diff = $opname->difference;

            if (abs($diff) > 0.00001) {
                $stock = Stock::where('product_variant_id', $variantId)->lockForUpdate()->firstOrFail();

                // Revert stock
                $stock->current_stock -= $diff;
                $stock->save();

                // Delete associated movement
                StockMovement::where('product_variant_id', $variantId)
                    ->where('movement_type', StockMovementTypeEnum::OPNAME)
                    ->where('notes', 'like', 'Penyesuaian opname%')
                    ->where('created_at', '>=', $opname->created_at->subSeconds(5))
                    ->delete();
            }

            return $opname->delete();
        });
    }
}