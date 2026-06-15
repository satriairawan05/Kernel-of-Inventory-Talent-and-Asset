<?php

namespace App\Services;

use App\Enums\StockMovementTypeEnum;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Create a new stock record.
     *
     * @param array $data
     * @return \App\Models\Stock
     */
    public function store(array $data): Stock
    {
        return DB::transaction(function () use ($data) {
            $stock = Stock::create($data);

            StockMovement::create([
                'product_variant_id' => $stock->product_variant_id,
                'movement_type'      => StockMovementTypeEnum::OPENING,
                'qty'                => $stock->current_stock,
                'stock_before'       => 0,
                'stock_after'        => $stock->current_stock,
                'notes'              => 'Initial stock entry',
                'pic_id'             => auth()->user()->id
            ]);

            return $stock;
        });
    }

    /**
     * Update an existing stock record.
     *
     * @param \App\Models\Stock $stock
     * @param array $data
     * @return \App\Models\Stock
     */
    public function update(Stock $stock, array $data): Stock
    {
        return DB::transaction(function () use ($stock, $data) {
            $oldStock = $stock->current_stock;
            $stock->update($data);
            $newStock = $stock->fresh()->current_stock;

            // Jika stok berubah, catat movement adjustment
            if (bccomp((string)$oldStock, (string)$newStock, 2) !== 0) {
                $difference = $newStock - $oldStock;

                StockMovement::create([
                    'product_variant_id' => $stock->product_variant_id,
                    'movement_type'      => StockMovementTypeEnum::ADJUSTMENT,
                    'qty'                => $difference,
                    'stock_before'       => $oldStock,
                    'stock_after'        => $newStock,
                    'notes'              => 'Manual stock adjustment',
                    'pic_id'             => auth()->user()->id
                ]);
            }

            return $stock->fresh();
        });
    }

    /**
     * Delete a stock record.
     *
     * @param \App\Models\Stock $stock
     * @return bool
     */
    public function destroy(Stock $stock): bool
    {
        return DB::transaction(function () use ($stock) {
             $currentStock = $stock->current_stock;

            if ($currentStock > 0) {
                StockMovement::create([
                    'product_variant_id' => $stock->product_variant_id,
                    'movement_type'      => StockMovementTypeEnum::ADJUSTMENT,
                    'qty'                => -$currentStock,
                    'stock_before'       => $currentStock,
                    'stock_after'        => 0,
                    'notes'              => 'Stock deleted (variant removed)',
                    'pic_id'             => auth()->user()->id
                ]);
            }

            return $stock->delete();
        });
    }
}
