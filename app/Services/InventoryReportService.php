<?php

namespace App\Services;

use App\Models\InventoryReport;
use App\Models\InventoryReportItem;
use App\Models\ProductVariant;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\ReportPeriod;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Service untuk mengelola laporan inventory (harian, mingguan, bulanan)
 * 
 * Semua laporan berbasis shift melalui relasi ke report_periods.
 */
class InventoryReportService
{
    /**
     * Generate laporan harian berdasarkan shift dan tanggal.
     *
     * @param string $date Format Y-m-d
     * @param int $periodId ID dari report_periods (terhubung ke shift)
     * @param string $location Lokasi toko/cabang
     * @param string $reportedBy Nama pelapor
     * @param string|null $openedAt Waktu buka (datetime) - jika null, ambil dari shift
     * @param string|null $closedAt Waktu tutup (datetime) - jika null, ambil dari shift
     * @return InventoryReport
     * @throws Exception
     */
    public function generateDailyReport(
        string $date,
        int $periodId,
        string $location,
        string $reportedBy,
        ?string $openedAt = null,
        ?string $closedAt = null
    ): InventoryReport {
        // Validasi periode dan ambil shift terkait
        $period = ReportPeriod::with('shift')->find($periodId);
        if (!$period || !$period->shift) {
            throw new Exception('Periode atau shift tidak ditemukan.');
        }

        // Tentukan rentang waktu: gunakan input atau ambil dari shift
        $start = $openedAt
            ? Carbon::parse($openedAt)
            : Carbon::parse($date . ' ' . ($period->shift->start_time ?? '00:00:00'));

        $end = $closedAt
            ? Carbon::parse($closedAt)
            : Carbon::parse($date . ' ' . ($period->shift->end_time ?? '23:59:59'));

        DB::beginTransaction();

        try {
            // 1. Buat header laporan - gunakan report_period_id
            $report = InventoryReport::create([
                'report_period_id'   => $periodId,        // <-- perbaikan: period_id -> report_period_id
                'location'           => $location,
                'reported_by'        => $reportedBy,
                'report_date'        => $date,
                'opened_at'          => $start,
                'closed_at'          => $end,
                'total_products_sold'=> 0,
                'created_by'         => auth()->id(),
                'notes'              => null,
            ]);

            // 2. Ambil semua produk aktif
            $products = ProductVariant::where('is_active', true)->get();
            $totalSold = 0;

            foreach ($products as $product) {
                // Stok awal (sebelum periode)
                $firstStock = Stock::where('product_variant_id', $product->id)
                    ->where('created_at', '<', $start)
                    ->latest('created_at')
                    ->value('current_stock') ?? 0;

                // Stok masuk (pembelian, opening, adjustment in)
                $stockIn = StockMovement::where('product_variant_id', $product->id)
                    ->whereBetween('created_at', [$start, $end])
                    ->whereIn('movement_type', ['purchasing', 'opening', 'in'])
                    ->sum('qty');

                // Terjual (penjualan, out) - ambil absolut
                $sellingQty = StockMovement::where('product_variant_id', $product->id)
                    ->whereBetween('created_at', [$start, $end])
                    ->whereIn('movement_type', ['sold', 'out'])
                    ->sum('qty');

                $selling = abs($sellingQty);
                $remain = $firstStock + $stockIn - $selling;

                // Simpan item laporan
                InventoryReportItem::create([
                    'inventory_report_id' => $report->id,
                    'product_variant_id'  => $product->id,
                    'first_stock'         => $firstStock,
                    'stock_in'            => $stockIn,
                    'selling'             => $selling,
                    'remain'              => $remain,
                ]);

                $totalSold += $selling;
            }

            // 3. Update total produk terjual di header
            $report->update(['total_products_sold' => $totalSold]);

            DB::commit();

            return $report->load('items.productVariant');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Gagal generate laporan: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Ambil laporan harian berdasarkan filter daily/weekly/monthly.
     *
     * @param string $type 'daily'|'weekly'|'monthly'
     * @param string|null $date Referensi tanggal (default hari ini)
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \InvalidArgumentException
     */
    public function getReports(string $type, ?string $date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();

        $query = InventoryReport::with(['period.shift', 'items.productVariant']);

        switch ($type) {
            case 'daily':
                $query->whereDate('report_date', $date);
                break;
            case 'weekly':
                $start = $date->copy()->startOfWeek();
                $end   = $date->copy()->endOfWeek();
                $query->whereBetween('report_date', [$start, $end]);
                break;
            case 'monthly':
                $start = $date->copy()->startOfMonth();
                $end   = $date->copy()->endOfMonth();
                $query->whereBetween('report_date', [$start, $end]);
                break;
            default:
                throw new \InvalidArgumentException('Tipe laporan tidak didukung: ' . $type);
        }

        return $query->orderBy('report_date', 'desc')->get();
    }

    /**
     * Ambil data agregat per produk dari laporan harian dalam rentang weekly/monthly.
     *
     * @param string $type 'weekly'|'monthly'
     * @param string|null $date
     * @return array
     */
    public function getAggregatedReport(string $type, ?string $date = null): array
    {
        $reports = $this->getReports($type, $date);
        if ($reports->isEmpty()) {
            return [];
        }

        // Kumpulkan semua product_variant_id yang muncul
        $productIds = $reports->flatMap->items->pluck('product_variant_id')->unique();
        $products = ProductVariant::whereIn('id', $productIds)->get()->keyBy('id');

        $aggregated = [];

        foreach ($reports as $report) {
            foreach ($report->items as $item) {
                $pid = $item->product_variant_id;
                if (!isset($aggregated[$pid])) {
                    $aggregated[$pid] = [
                        'product_variant' => $products[$pid] ?? null,
                        'first_stock'     => 0,
                        'stock_in'        => 0,
                        'selling'         => 0,
                        'remain'          => 0,
                    ];
                }

                // first_stock diambil dari laporan pertama (hari pertama)
                if ($aggregated[$pid]['first_stock'] == 0) {
                    $aggregated[$pid]['first_stock'] = $item->first_stock;
                }

                $aggregated[$pid]['stock_in'] += $item->stock_in;
                $aggregated[$pid]['selling']  += $item->selling;
                // remain diambil dari laporan terakhir
                $aggregated[$pid]['remain'] = $item->remain;
            }
        }

        // Hitung ulang remain = first_stock + stock_in - selling
        foreach ($aggregated as &$data) {
            $data['remain'] = $data['first_stock'] + $data['stock_in'] - $data['selling'];
        }

        return array_values($aggregated);
    }

    /**
     * Hapus laporan harian beserta item-itemnya.
     *
     * @param int $reportId
     * @return bool
     * @throws Exception
     */
    public function deleteReport(int $reportId): bool
    {
        $report = InventoryReport::find($reportId);
        if (!$report) {
            throw new Exception('Laporan tidak ditemukan.');
        }

        DB::beginTransaction();
        try {
            $report->delete();
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Gagal hapus laporan: ' . $e->getMessage());
            throw $e;
        }
    }
}