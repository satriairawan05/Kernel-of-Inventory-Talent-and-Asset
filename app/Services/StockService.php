<?php

namespace App\Services;

use App\Models\Stock;
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
            return Stock::create($data);
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
            $stock->update($data);
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
            return $stock->delete();
        });
    }
}