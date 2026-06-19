<?php

namespace App\Services;

use App\Models\InventoryReport;
use Carbon\Carbon;
use Illuminate\View\View;

/**
 * Service untuk menampilkan preview laporan inventory di layar
 * sebelum dicetak (mirip struk thermal 58mm).
 */
class InventoryPreviewService
{
    /**
     * Preview satu laporan harian.
     *
     * @param int $reportId
     * @return View
     * @throws \Exception
     */
    public function previewDailyReport(int $reportId): View
    {
        $report = InventoryReport::with(['period.shift', 'items.productVariant'])
            ->find($reportId);

        if (!$report) {
            throw new \Exception('Laporan tidak ditemukan.');
        }

        return view('admin.inventory.report.preview.invoice', [
            'report' => $report,
            'type'   => 'daily',
        ]);
    }

    /**
     * Preview laporan agregat (weekly/monthly).
     *
     * @param string $type 'weekly'|'monthly'
     * @param string|null $date
     * @param array $data Data dari InventoryReportService::getAggregatedReport()
     * @return View
     */
    public function previewAggregatedReport(string $type, ?string $date, array $data): View
    {
        return view('admin.inventory.report.preview.invoice',[
            'type'   => $type,
            'date'   => $date,
            'data'   => $data,
            'isAggregated' => true,
        ]);
    }
}