<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockMovement;
use App\Enums\StockMovementTypeEnum;
use Illuminate\Support\Facades\DB;

class StockInService
{
    /**
     * Create a new stock-in transaction (purchase).
     *
     * @param array $data
     * @return \App\Models\StockMovement
     */
    public function store(array $data): StockMovement
    {
        return DB::transaction(function () use ($data) {
            $variantId = $data['product_variant_id'];
            $qty = $data['qty'];

            // Get current stock
            $stock = Stock::where('product_variant_id', $variantId)->first();

            if (!$stock) {
                // If no stock record exists, create one with 0 stock
                $stock = Stock::create([
                    'product_variant_id' => $variantId,
                    'current_stock' => 0,
                ]);
            }

            $stockBefore = $stock->current_stock;
            $stockAfter = $stockBefore + $qty;

            // Update stock
            $stock->update(['current_stock' => $stockAfter]);

            // Create movement record
            return StockMovement::create([
                'product_variant_id' => $variantId,
                'movement_type' => StockMovementTypeEnum::PURCHASE,
                'qty' => $qty,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }

    /**
     * Update an existing stock-in transaction.
     *
     * @param \App\Models\StockMovement $movement
     * @param array $data
     * @return \App\Models\StockMovement
     */
    public function update(StockMovement $movement, array $data): StockMovement
    {
        return DB::transaction(function () use ($movement, $data) {
            $variantId = $movement->product_variant_id;
            $oldQty = $movement->qty;
            $newQty = $data['qty'];

            // Get current stock
            $stock = Stock::where('product_variant_id', $variantId)->firstOrFail();

            // Reverse the old movement
            $stockBefore = $stock->current_stock - $oldQty;

            // Apply new qty
            $stockAfter = $stockBefore + $newQty;

            // Update stock
            $stock->update(['current_stock' => $stockAfter]);

            // Update movement record
            $movement->update([
                'qty' => $newQty,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'notes' => $data['notes'] ?? null,
            ]);

            return $movement->fresh();
        });
    }

    /**
     * Delete a stock-in transaction (reverse the movement).
     *
     * @param \App\Models\StockMovement $movement
     * @return bool
     */
    public function destroy(StockMovement $movement): bool
    {
        return DB::transaction(function () use ($movement) {
            $variantId = $movement->product_variant_id;
            $qty = $movement->qty;

            // Get current stock
            $stock = Stock::where('product_variant_id', $variantId)->firstOrFail();

            // Reverse the movement (subtract qty)
            $newStock = $stock->current_stock - $qty;
            $stock->update(['current_stock' => $newStock]);

            // Delete the movement record
            return $movement->delete();
        });
    }
}