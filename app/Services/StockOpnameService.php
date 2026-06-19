<?php

namespace App\Services;

use App\Enums\StockMovementTypeEnum;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\StockOpnameDetail;
use App\Models\StockOpnamePeriod;
use Illuminate\Support\Facades\DB;

class StockOpnameService
{
    /**
     * Create new opname period with details.
     */
    public function storePeriod(array $data, array $details): StockOpnamePeriod
    {
        return DB::transaction(function () use ($data, $details) {
            $period = StockOpnamePeriod::create([
                'period_start' => $data['period_start'],
                'period_end'   => $data['period_end'],
                'status'       => 'active',
                'notes'        => $data['notes'] ?? null,
            ]);

            foreach ($details as $variantId => $physicalStock) {
                $stock = Stock::where('product_variant_id', $variantId)->first();
                $systemStock = $stock ? $stock->current_stock : 0;
                $difference = $physicalStock - $systemStock;

                StockOpnameDetail::create([
                    'stock_opname_period_id' => $period->id,
                    'product_variant_id'     => $variantId,
                    'system_stock'           => $systemStock,
                    'physical_stock'         => $physicalStock,
                    'difference'             => $difference,
                    'notes'                  => $data['detail_notes'][$variantId] ?? null,
                ]);

                // Jika selisih tidak nol, sesuaikan stok & catat movement
                if (abs($difference) > 0.00001) {
                    if (!$stock) {
                        $stock = Stock::create([
                            'product_variant_id' => $variantId,
                            'current_stock'      => 0,
                        ]);
                    }
                    $stock->current_stock = $physicalStock;
                    $stock->last_update_at = now();
                    $stock->save();

                    StockMovement::create([
                        'product_variant_id' => $variantId,
                        'movement_type'      => StockMovementTypeEnum::OPNAME,
                        'qty'                => $difference,
                        'stock_before'       => $systemStock,
                        'stock_after'        => $physicalStock,
                        'notes'              => "Opname periode {$period->period_start} s/d {$period->period_end}",
                        'user_id'            => auth()->id(),
                    ]);
                }
            }

            return $period;
        });
    }

    /**
     * Update a single detail (physical stock).
     */
    public function updateDetail(StockOpnameDetail $detail, array $data): StockOpnameDetail
    {
        if ($detail->period->status === 'closed') {
            throw new \Exception('Periode sudah ditutup, tidak bisa diubah.');
        }

        return DB::transaction(function () use ($detail, $data) {
            $newPhysical = $data['physical_stock'];
            $oldPhysical = $detail->physical_stock ?? $detail->system_stock;

            // Jika belum pernah diupdate, system_stock dianggap sebagai stok awal
            $systemStock = $detail->system_stock;
            $difference = $newPhysical - $systemStock;

            $detail->physical_stock = $newPhysical;
            $detail->difference = $difference;
            $detail->notes = $data['notes'] ?? $detail->notes;
            $detail->save();

            // Sesuaikan stok & movement
            $stock = Stock::where('product_variant_id', $detail->product_variant_id)->firstOrFail();
            $stock->current_stock = $newPhysical;
            $stock->save();

            // Cek apakah sudah ada movement untuk opname ini sebelumnya? Kita bisa hapus yang lama lalu buat baru
            // Untuk sederhana, kita catat movement baru setiap update (bisa jadi banyak). Atau kita timpa?
            // Lebih baik: hapus movement yang terkait dengan detail ini jika ada, lalu buat baru.
            StockMovement::where('product_variant_id', $detail->product_variant_id)
                ->where('movement_type', StockMovementTypeEnum::OPNAME)
                ->where('notes', 'like', '%Opname periode%')
                ->where('created_at', '>=', $detail->period->created_at)
                ->delete();

            if (abs($difference) > 0.00001) {
                StockMovement::create([
                    'product_variant_id' => $detail->product_variant_id,
                    'movement_type'      => StockMovementTypeEnum::OPNAME,
                    'qty'                => $difference,
                    'stock_before'       => $systemStock,
                    'stock_after'        => $newPhysical,
                    'notes'              => "Opname periode {$detail->period->period_start} s/d {$detail->period->period_end} (update)",
                    'user_id'            => auth()->id(),
                ]);
            }

            return $detail;
        });
    }

    /**
     * Close period (status = closed).
     */
    public function closePeriod(StockOpnamePeriod $period): StockOpnamePeriod
    {
        if ($period->status === 'closed') {
            throw new \Exception('Periode sudah ditutup.');
        }
        $period->status = 'closed';
        $period->save();

        return $period;
    }

    /**
     * Get paginated periods with stats.
     */
    public function getPeriods()
    {
        return StockOpnamePeriod::with('details')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    /**
     * Get a period with its details loaded.
     */
    public function getPeriodWithDetails(StockOpnamePeriod $period): StockOpnamePeriod
    {
        return $period->load('details.productVariant.product');
    }
}
