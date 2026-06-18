<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockMovement;
use App\Enums\StockMovementTypeEnum;
use Illuminate\Support\Facades\DB;

class StockOutService
{
    /**
     * Create a new stock-out transaction (sale).
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

            // Check if stock is sufficient
            if ($stock->current_stock < $qty) {
                throw new \Exception('Stok tidak mencukupi untuk pengeluaran.');
            }

            $stockBefore = $stock->current_stock;
            $stockAfter = $stockBefore - $qty;

            // Update stock (reduce)
            $stock->update(['current_stock' => $stockAfter]);

            // Create movement record (qty stored as negative value for out)
            return StockMovement::create([
                'product_variant_id' => $variantId,
                'movement_type'      => $data['movement_type'],
                'qty'                => -$qty, // Negative indicates stock out
                'stock_before'       => $stockBefore,
                'stock_after'        => $stockAfter,
                'notes'              => $data['notes'] ?? null,
                'user_id'            => auth()->user()->id,
            ]);
        });
    }

    /**
     * Update an existing stock-out transaction.
     *
     * @param \App\Models\StockMovement $movement
     * @param array $data
     * @return \App\Models\StockMovement
     */
    public function update(StockMovement $movement, array $data): StockMovement
    {
        return DB::transaction(function () use ($movement, $data) {
            $variantId = $movement->product_variant_id;
            $oldQty = abs($movement->qty); // Positive value (stock out quantity)
            $newQty = $data['qty'];

            // Get current stock
            $stock = Stock::where('product_variant_id', $variantId)->firstOrFail();

            // Reverse the old movement (add back the old qty)
            $stockBefore = $stock->current_stock + $oldQty;

            // Check if stock is sufficient for new qty
            if ($stockBefore < $newQty) {
                throw new \Exception('Stok tidak mencukupi untuk pengeluaran.');
            }

            // Apply new qty (subtract)
            $stockAfter = $stockBefore - $newQty;

            // Update stock
            $stock->update(['current_stock' => $stockAfter]);

            // Update movement record (qty stored as negative)
            $movement->update([
                'qty'           => -$newQty,
                'movement_type' => $data['movement_type'],
                'stock_before'  => $stockBefore,
                'stock_after'   => $stockAfter,
                'notes'         => $data['notes'] ?? null,
                'user_id'       => auth()->user()->id,
            ]);

            return $movement->fresh();
        });
    }

    /**
     * Delete a stock-out transaction (reverse the movement).
     *
     * @param \App\Models\StockMovement $movement
     * @return bool
     */
    public function destroy(StockMovement $movement): bool
    {
        return DB::transaction(function () use ($movement) {
            $variantId = $movement->product_variant_id;
            $qty = abs($movement->qty); // Positive value

            // Get current stock
            $stock = Stock::where('product_variant_id', $variantId)->firstOrFail();

            // Reverse the movement (add back qty)
            $newStock = $stock->current_stock + $qty;
            $stock->update(['current_stock' => $newStock]);

            // Delete the movement record
            return $movement->delete();
        });
    }
}