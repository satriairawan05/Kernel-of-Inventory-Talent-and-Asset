<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalesReportStoreRequest;
use App\Http\Requests\SalesReportUpdateRequest;
use App\Models\Company;
use App\Models\SalesReport;
use App\Services\SalesReportQueryService;
use App\Services\SalesReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SalesReportController extends Controller
{
    /**
     * Constructor for Controller.
     */
    public function __construct(private $access = [])
    {
        //
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Display a daily listing of the resource.
     */
    public function dailyIndex(SalesReportQueryService $salesReportQueryService)
    {
        try {
            $data = $salesReportQueryService->getPaginatedReports(10);

            return view('admin.pos.sales_report.daily.index', ['reports' => $data]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Display a weekly listing of the resource.
     */
    public function weeklyIndex(SalesReportQueryService $salesReportQueryService, Request $request)
    {
        try {
            $company = Company::query()->latest('id')->get();
            $startDate = $request->filled('start_date')
                ? $request->start_date
                : now()->startOfMonth()->toDateString();
            $endDate = $request->filled('end_date')
                ? $request->end_date
                : now()->toDateString();
            $companyId = $request->filled('company_id') ? (int) $request->company_id : null;

            $data = $salesReportQueryService->getWeeklyReportForTable($startDate, $endDate, $companyId);

            return view('admin.pos.sales_report.weekly.index', [
                'weeklyReports' => $data,
                'companies' => $company,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Display a monthlylisting of the resource.
     */
    public function monthlyIndex(SalesReportQueryService $salesReportQueryService, Request $request)
    {
        try {
            $company = Company::query()->latest('id')->get();
            $companyId = $request->filled('company_id') ? (int) $request->company_id : null;

            $startDate = $request->filled('start_date')
                ? Carbon::createFromFormat('Y-m', $request->start_date)->startOfMonth()->toDateString()
                : now()->startOfMonth()->toDateString();
            $endDate = $request->filled('end_date')
                ? Carbon::createFromFormat('Y-m', $request->end_date)->endOfMonth()->toDateString()
                : now()->endOfMonth()->toDateString();

            $data = $salesReportQueryService->getMonthlyReportForTable($startDate, $endDate, $companyId);

            return view('admin.pos.sales_report.monthly.index', [
                'companies' => $company,
                'monthlyReports' => $data,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return view('admin.pos.sales_report.daily.create', [
                'companies' => Company::query()->get()
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SalesReportStoreRequest $request, SalesReportService $salesReportService)
    {
        try {
            $salesReportService->store($request->validated());

            return redirect()->route('pos.report.daily')->with('success', 'New Report Created Successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Display the specified resource in Daily.
     */
    public function show(SalesReport $salesReport, SalesReportQueryService $salesReportQueryService)
    {
        try {
            $newReport = $salesReport->find(request()->segment(3));
            $reportDate = $salesReport->report_date ?? $newReport->arrived_date ?? now()->toDateString();
            $data = $salesReportQueryService->getDailyDetailReport($reportDate, $newReport->company_id);

            return view('admin.pos.sales_report.daily.show', [
                'salesReport' => $data,
                'report' => $salesReport,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Display the specified resource in Weekly.
     */
    public function showWeekly(Request $request, SalesReportQueryService $salesReportQueryService)
    {
        try {
            $startDate = $request->route('start_date') ?? $request->query('start_date');
            $endDate = $request->route('end_date') ?? $request->query('end_date');
            $companyId = $request->route('company_id') ?? $request->query('company_id');

            $report = $salesReportQueryService->getWeeklyReport(
                (string) $startDate,
                (string) $endDate,
                $companyId ? (int) $companyId : null,
            );

            return view('admin.pos.sales_report.weekly.show', [
                'report' => $report,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'companyId' => $companyId ? (int) $companyId : null,
                'companies' => Company::query()->latest('id')->get(),
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Display the specified resource in monthly.
     */
    public function showMonthly(Request $request, SalesReportQueryService $salesReportQueryService)
    {
        try {
            $startDate = $request->route('start_date') ?? $request->query('start_date');
            $endDate = $request->route('end_date') ?? $request->query('end_date');
            $companyId = $request->route('company_id') ?? $request->query('company_id');

            $report = $salesReportQueryService->getMonthlyReport(
                (string) $startDate,
                (string) $endDate,
                $companyId ? (int) $companyId : null,
            );

            return view('admin.pos.sales_report.monthly.show', [
                'report' => $report,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'companyId' => $companyId ? (int) $companyId : null,
                'companies' => Company::query()->latest('id')->get(),
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalesReport $salesReport)
    {
        try {
            return view('admin.pos.sales_report.daily.edit', [
                'companies' => Company::query()->get(),
                'report' => SalesReport::findOrFail(request()->segment(3))
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SalesReportUpdateRequest $request, SalesReport $salesReport, SalesReportService $salesReportService)
    {
        try {
            $salesReportService->update(SalesReport::findOrFail(request()->segment(3)), $request->validated());

            return redirect()->route('pos.report.daily')->with('success', 'Report Updated Successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesReport $salesReport, SalesReportService $salesReportService)
    {
        try {
            $salesReportService->destroy(SalesReport::findOrFail(request()->segment(3)));

            return redirect()->back()->with('success', 'Report Deleted!');
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }
}
