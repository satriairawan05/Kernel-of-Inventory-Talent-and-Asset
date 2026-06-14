<?php

namespace App\Services;

use App\Models\SalesReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SalesReportQueryService
{
    /**
     * Get all sales reports with pagination for index table.
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getPaginatedReports(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = SalesReport::query()
            ->with('company')
            ->orderBy('report_date', 'DESC');

        // Apply filters
        if (!empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('report_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('report_date', '<=', $filters['end_date']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('notes', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Get daily sales report for index table.
     *
     * @param string $startDate
     * @param string $endDate
     * @param int|null $companyId
     * @return Collection
     */
    public function getDailyReportForTable(string $startDate, string $endDate, ?int $companyId = null): Collection
    {
        $query = SalesReport::query()
            ->whereBetween('report_date', [$startDate, $endDate])
            ->select(
                'report_date',
                DB::raw('SUM(accessories_amount) as total_accessories'),
                DB::raw('SUM(service_amount) as total_service'),
                DB::raw('SUM(pulsa_amount) as total_pulsa'),
                DB::raw('SUM(total_amount) as grand_total'),
                DB::raw('COUNT(*) as total_transactions')
            )
            ->groupBy('report_date')
            ->orderBy('report_date', 'DESC');

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        return $query->get()->map(function ($report) {
            return (object) [
                'report_date' => $report->report_date,
                'arrived_date' => $report->arrived_date,
                'accessories_amount' => $report->total_accessories,
                'service_amount' => $report->total_service,
                'pulsa_amount' => $report->total_pulsa,
                'total_amount' => $report->grand_total,
                'total_transactions' => $report->total_transactions,
                'formatted_date' => Carbon::parse($report->report_date)->format('d/m/Y'),
                'formatted_accessories' => \Carbon\Carbon::rupiah($report->total_accessories, 0, ',', '.'),
                'formatted_service' => \Carbon\Carbon::rupiah($report->total_service, 0, ',', '.'),
                'formatted_pulsa' => \Carbon\Carbon::rupiah($report->total_pulsa, 0, ',', '.'),
                'formatted_total' => \Carbon\Carbon::rupiah($report->grand_total, 0, ',', '.'),
            ];
        });
    }

    /**
     * Get weekly sales report for index table.
     *
     * @param string $startDate
     * @param string $endDate
     * @param int|null $companyId
     * @return Collection
     */
    public function getWeeklyReportForTable(string $startDate, string $endDate, ?int $companyId = null): Collection
    {
        $query = SalesReport::query()
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('report_date', [$startDate, $endDate])
                  ->orWhereBetween('arrived_date', [$startDate, $endDate]);
            })
            ->select(
                DB::raw('YEAR(report_date) as year'),
                DB::raw('WEEK(report_date, 1) as week_number'),
                DB::raw('MIN(report_date) as week_start'),
                DB::raw('MAX(report_date) as week_end'),
                DB::raw('COALESCE(SUM(accessories_amount), 0) as total_accessories'),
                DB::raw('COALESCE(SUM(service_amount), 0) as total_service'),
                DB::raw('COALESCE(SUM(pulsa_amount), 0) as total_pulsa'),
                DB::raw('COALESCE(SUM(total_amount), 0) as grand_total'),
                DB::raw('COUNT(*) as total_transactions')
            )
            ->groupBy('year', 'week_number')
            ->orderBy('year', 'DESC')
            ->orderBy('week_number', 'DESC');

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        return $query->get()->map(function ($report) {
            return (object) [
                'week_display' => "Week {$report->week_number} ({$report->week_start} to {$report->week_end})",
                'year' => $report->year,
                'week_number' => $report->week_number,
                'week_start' => $report->week_start,
                'week_end' => $report->week_end,
                'accessories_amount' => $report->total_accessories,
                'service_amount' => $report->total_service,
                'pulsa_amount' => $report->total_pulsa,
                'total_amount' => $report->grand_total,
                'total_transactions' => $report->total_transactions,
                'formatted_accessories' => \Carbon\Carbon::rupiah($report->total_accessories, 0, ',', '.'),
                'formatted_service' => \Carbon\Carbon::rupiah($report->total_service, 0, ',', '.'),
                'formatted_pulsa' => \Carbon\Carbon::rupiah($report->total_pulsa, 0, ',', '.'),
                'formatted_total' => \Carbon\Carbon::rupiah($report->grand_total, 0, ',', '.'),
            ];
        });
    }

    /**
     * Get monthly sales report for index table.
     *
     * @param string $startDate
     * @param string $endDate
     * @param int|null $companyId
     * @return Collection
     */
    public function getMonthlyReportForTable(string $startDate, string $endDate, ?int $companyId = null): Collection
    {
        $query = SalesReport::query()
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('report_date', [$startDate, $endDate])
                  ->orWhereBetween('arrived_date', [$startDate, $endDate]);
            })
            ->select(
                DB::raw('YEAR(report_date) as year'),
                DB::raw('MONTH(report_date) as month'),
                DB::raw('DATE_FORMAT(report_date, "%Y-%m") as month_year'),
                DB::raw('DATE_FORMAT(arrived_date, "%Y-%m") as arrived_month_year'),
                DB::raw('COALESCE(SUM(accessories_amount), 0) as total_accessories'),
                DB::raw('COALESCE(SUM(service_amount), 0) as total_service'),
                DB::raw('COALESCE(SUM(pulsa_amount), 0) as total_pulsa'),
                DB::raw('COALESCE(SUM(total_amount), 0) as grand_total'),
                DB::raw('COUNT(*) as total_transactions')
            )
            ->groupBy('year', 'month', 'month_year','arrived_month_year')
            ->orderBy('year', 'DESC')
            ->orderBy('month', 'DESC');

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        return $query->get()->map(function ($report) {
            $monthName = Carbon::createFromDate($report->year, $report->month, 1)->format('F');
            
            return (object) [
                'month_display' => "{$monthName} {$report->year}",
                'year' => $report->year,
                'month' => $report->month,
                'month_name' => $monthName,
                'month_year' => $report->month_year,
                'accessories_amount' => $report->total_accessories,
                'service_amount' => $report->total_service,
                'pulsa_amount' => $report->total_pulsa,
                'total_amount' => $report->grand_total,
                'total_transactions' => $report->total_transactions,
                'formatted_accessories' => \Carbon\Carbon::rupiah($report->total_accessories, 0, ',', '.'),
                'formatted_service' => \Carbon\Carbon::rupiah($report->total_service, 0, ',', '.'),
                'formatted_pulsa' => \Carbon\Carbon::rupiah($report->total_pulsa, 0, ',', '.'),
                'formatted_total' => \Carbon\Carbon::rupiah($report->grand_total, 0, ',', '.'),
            ];
        });
    }

    /**
     * Get sales report per company for index table.
     *
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getReportPerCompanyForTable(string $startDate, string $endDate): Collection
    {
        $reports = SalesReport::query()
            ->whereBetween('report_date', [$startDate, $endDate])
            ->with('company')
            ->select(
                'company_id',
                DB::raw('SUM(accessories_amount) as total_accessories'),
                DB::raw('SUM(service_amount) as total_service'),
                DB::raw('SUM(pulsa_amount) as total_pulsa'),
                DB::raw('SUM(total_amount) as grand_total'),
                DB::raw('COUNT(*) as total_transactions')
            )
            ->groupBy('company_id')
            ->get();

        return $reports->map(function ($report) {
            return (object) [
                'company_id' => $report->company_id,
                'company_name' => $report->company->name ?? 'N/A',
                'accessories_amount' => $report->total_accessories,
                'service_amount' => $report->total_service,
                'pulsa_amount' => $report->total_pulsa,
                'total_amount' => $report->grand_total,
                'total_transactions' => $report->total_transactions,
                'formatted_accessories' => \Carbon\Carbon::rupiah($report->total_accessories, 0, ',', '.'),
                'formatted_service' => \Carbon\Carbon::rupiah($report->total_service, 0, ',', '.'),
                'formatted_pulsa' => \Carbon\Carbon::rupiah($report->total_pulsa, 0, ',', '.'),
                'formatted_total' => \Carbon\Carbon::rupiah($report->grand_total, 0, ',', '.'),
            ];
        });
    }

    /**
     * Get daily sales report based on date range.
     *
     * @param string $startDate (Y-m-d)
     * @param string $endDate (Y-m-d)
     * @param int|null $companyId
     * @return array
     */
    public function getDailyReport(string $startDate, string $endDate, ?int $companyId = null): array
    {
        $query = SalesReport::query()
            ->whereBetween('report_date', [$startDate, $endDate])
            ->select(
                'report_date',
                DB::raw('SUM(accessories_amount) as total_accessories'),
                DB::raw('SUM(service_amount) as total_service'),
                DB::raw('SUM(pulsa_amount) as total_pulsa'),
                DB::raw('SUM(total_amount) as grand_total'),
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('GROUP_CONCAT(notes SEPARATOR " | ") as all_notes')
            )
            ->groupBy('report_date')
            ->orderBy('report_date', 'ASC');

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $dailyReports = $query->get();

        $formattedReports = $dailyReports->map(function ($report) {
            return [
                'report_date' => $report->report_date,
                'arrived_date' => $report->arrived_date,
                'accessories_amount' => $report->total_accessories,
                'service_amount' => $report->total_service,
                'pulsa_amount' => $report->total_pulsa,
                'total_amount' => $report->grand_total,
                'total_transactions' => $report->total_transactions,
                'notes' => $report->all_notes,
            ];
        });

        return [
            'report_type' => 'daily',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'data' => $formattedReports,
            'summary' => [
                'total_days' => $dailyReports->count(),
                'total_accessories' => $dailyReports->sum('total_accessories'),
                'total_service' => $dailyReports->sum('total_service'),
                'total_pulsa' => $dailyReports->sum('total_pulsa'),
                'grand_total' => $dailyReports->sum('grand_total'),
                'total_transactions' => $dailyReports->sum('total_transactions'),
            ],
        ];
    }

    /**
     * Get weekly sales report based on date range.
     *
     * @param string $startDate (Y-m-d)
     * @param string $endDate (Y-m-d)
     * @param int|null $companyId
     * @return array
     */
    public function getWeeklyReport(string $startDate, string $endDate, ?int $companyId = null): array
    {
        $query = SalesReport::query()
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('report_date', [$startDate, $endDate])
                  ->orWhereBetween('arrived_date', [$startDate, $endDate]);
            })
            ->select(
                DB::raw('YEAR(report_date) as year'),
                DB::raw('WEEK(report_date, 1) as week_number'),
                DB::raw('MIN(report_date) as week_start'),
                DB::raw('MAX(report_date) as week_end'),
                DB::raw('SUM(accessories_amount) as total_accessories'),
                DB::raw('SUM(service_amount) as total_service'),
                DB::raw('SUM(pulsa_amount) as total_pulsa'),
                DB::raw('SUM(total_amount) as grand_total'),
                DB::raw('COUNT(*) as total_transactions')
            )
            ->groupBy('year', 'week_number')
            ->orderBy('year', 'ASC')
            ->orderBy('week_number', 'ASC');

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $weeklyReports = $query->get();

        $formattedReports = $weeklyReports->map(function ($report) {
            return [
                'week_display' => "Week {$report->week_number} ({$report->week_start} to {$report->week_end})",
                'year' => $report->year,
                'week_number' => $report->week_number,
                'week_start' => $report->week_start,
                'week_end' => $report->week_end,
                'accessories_amount' => $report->total_accessories,
                'service_amount' => $report->total_service,
                'pulsa_amount' => $report->total_pulsa,
                'total_amount' => $report->grand_total,
                'total_transactions' => $report->total_transactions,
            ];
        });

        return [
            'report_type' => 'weekly',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'data' => $formattedReports,
            'summary' => [
                'total_weeks' => $weeklyReports->count(),
                'total_accessories' => $weeklyReports->sum('total_accessories'),
                'total_service' => $weeklyReports->sum('total_service'),
                'total_pulsa' => $weeklyReports->sum('total_pulsa'),
                'grand_total' => $weeklyReports->sum('grand_total'),
                'total_transactions' => $weeklyReports->sum('total_transactions'),
            ],
        ];
    }

    /**
     * Get monthly sales report based on date range.
     *
     * @param string $startDate (Y-m-d)
     * @param string $endDate (Y-m-d)
     * @param int|null $companyId
     * @return array
     */
    public function getMonthlyReport(string $startDate, string $endDate, ?int $companyId = null): array
    {
        $query = SalesReport::query()
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('report_date', [$startDate, $endDate])
                  ->orWhereBetween('arrived_date', [$startDate, $endDate]);
            })
            ->select(
                DB::raw('YEAR(report_date) as year'),
                DB::raw('MONTH(report_date) as month'),
                DB::raw('DATE_FORMAT(report_date, "%Y-%m") as month_year'),
                DB::raw('SUM(accessories_amount) as total_accessories'),
                DB::raw('SUM(service_amount) as total_service'),
                DB::raw('SUM(pulsa_amount) as total_pulsa'),
                DB::raw('SUM(total_amount) as grand_total'),
                DB::raw('COUNT(*) as total_transactions')
            )
            ->groupBy('year', 'month', 'month_year')
            ->orderBy('year', 'ASC')
            ->orderBy('month', 'ASC');

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $monthlyReports = $query->get();

        $formattedReports = $monthlyReports->map(function ($report) {
            $monthName = Carbon::createFromDate($report->year, $report->month, 1)->format('F');
            
            return [
                'month_display' => "{$monthName} {$report->year}",
                'year' => $report->year,
                'month' => $report->month,
                'month_name' => $monthName,
                'month_year' => $report->month_year,
                'accessories_amount' => $report->total_accessories,
                'service_amount' => $report->total_service,
                'pulsa_amount' => $report->total_pulsa,
                'total_amount' => $report->grand_total,
                'total_transactions' => $report->total_transactions,
            ];
        });

        return [
            'report_type' => 'monthly',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'data' => $formattedReports,
            'summary' => [
                'total_months' => $monthlyReports->count(),
                'total_accessories' => $monthlyReports->sum('total_accessories'),
                'total_service' => $monthlyReports->sum('total_service'),
                'total_pulsa' => $monthlyReports->sum('total_pulsa'),
                'grand_total' => $monthlyReports->sum('grand_total'),
                'total_transactions' => $monthlyReports->sum('total_transactions'),
            ],
        ];
    }

    /**
     * Get daily report with transaction details (per company).
     *
     * @param string $date
     * @param int|null $companyId
     * @return array
     */
    public function getDailyDetailReport(string $date, ?int $companyId = null): array
    {
        $query = SalesReport::query()
            ->where('report_date', $date)
            ->orWhere('arrived_date', $date)
            ->with('company');

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $reports = $query->get();      
        $company = \App\Models\Company::firstWhere('id', $reports[0]->company_id);

        return [
            'report_type' => 'daily_detail',
            'report_date' => $date,
            'arrived_date' => $date,
            'data' => $reports->map(function ($report) {
                return [
                    'id' => $report->id,
                    'company_id' => $report->company_id,
                    'company_name' => $report->company->name ?? null,
                    'accessories_amount' => $report->accessories_amount,
                    'service_amount' => $report->service_amount,
                    'pulsa_amount' => $report->pulsa_amount,
                    'total_amount' => $report->total_amount,
                    'notes' => $report->notes,
                    'created_at' => $report->created_at,
                ];
            }),
            'company_name' => $company->company_name, 
            'summary' => [
                'total_companies' => $reports->count(),
                'total_accessories' => $reports->sum('accessories_amount'),
                'total_service' => $reports->sum('service_amount'),
                'total_pulsa' => $reports->sum('pulsa_amount'),
                'grand_total' => $reports->sum('total_amount'),
            ],
        ];
    }

    /**
     * Get sales report per company (grouped by company).
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getReportPerCompany(string $startDate, string $endDate): array
    {
        $reports = SalesReport::query()
            ->whereBetween('report_date', [$startDate, $endDate])
            ->with('company')
            ->select(
                'company_id',
                DB::raw('SUM(accessories_amount) as total_accessories'),
                DB::raw('SUM(service_amount) as total_service'),
                DB::raw('SUM(pulsa_amount) as total_pulsa'),
                DB::raw('SUM(total_amount) as grand_total'),
                DB::raw('COUNT(*) as total_transactions')
            )
            ->groupBy('company_id')
            ->get();

        return [
            'report_type' => 'per_company',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'data' => $reports->map(function ($report) {
                return [
                    'company_id' => $report->company_id,
                    'company_name' => $report->company->name ?? null,
                    'accessories_amount' => $report->total_accessories,
                    'service_amount' => $report->total_service,
                    'pulsa_amount' => $report->total_pulsa,
                    'total_amount' => $report->grand_total,
                    'total_transactions' => $report->total_transactions,
                ];
            }),
            'summary' => [
                'total_companies' => $reports->count(),
                'overall_total' => $reports->sum('grand_total'),
                'overall_transactions' => $reports->sum('total_transactions'),
            ],
        ];
    }
}