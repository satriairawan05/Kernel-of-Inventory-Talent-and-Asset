<?php

namespace App\Services;

use App\Models\SalesReport;
use Illuminate\Support\Facades\DB;

class SalesReportService
{
    /**
     * Store a new transaction record (one by one).
     */
    public function store(array $data): SalesReport
    {
        return DB::transaction(function () use ($data) {
            return SalesReport::create([
                'company_id'         => $data['company_id'] ?? null,
                'report_date'        => $data['report_date'] ?? null,
                'arrived_date'       => $data['arrived_date'] ?? null,
                'accessories_amount' => $data['accessories_amount'] ?? 0,
                'service_amount'     => $data['service_amount'] ?? 0,
                'pulsa_amount'       => $data['pulsa_amount'] ?? 0,
                'total_amount'       => $data['total_amount'] ?? 0,
                'notes'              => $data['notes'] ?? null,
            ]);
        });
    }

    /**
     * Update an existing transaction record.
     */
    public function update(SalesReport $salesReport, array $data): SalesReport
    {
        return DB::transaction(function () use ($salesReport, $data) {
            $salesReport->update([
                'company_id'         => $data['company_id'] ?? $salesReport->company_id,
                'report_date'        => $data['report_date'] ?? $salesReport->report_date,
                'arrived_date'       => $data['arrived_date'] ?? $salesReport->arrived_date,
                'accessories_amount' => $data['accessories_amount'] ?? $salesReport->accessories_amount,
                'service_amount'     => $data['service_amount'] ?? $salesReport->service_amount,
                'pulsa_amount'       => $data['pulsa_amount'] ?? $salesReport->pulsa_amount,
                'total_amount'       => $data['total_amount'] ?? $salesReport->total_amount,
                'notes'              => $data['notes'] ?? $salesReport->notes,
            ]);

            return $salesReport->fresh();
        });
    }

    /**
     * Delete a transaction record.
     */
    public function destroy(SalesReport $salesReport): bool
    {
        return DB::transaction(function () use ($salesReport) {
            return $salesReport->delete();
        });
    }
}