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
            $salesReport = SalesReport::latest()->get();
            $company = Company::latest()->get();

            if ($request->start_date !== null && $request->end_date !== null && $request->company_id) {
                $data = $salesReportQueryService->getWeeklyReportForTable($request->start_date, $request->end_date, $request->company_id);

                return view('admin.pos.sales_report.weekly.index', [
                    'weeklyReports' => $data,
                    'companies' => $company,
                ]);
            }

            return view('admin.pos.sales_report.weekly.index', [
                'weeklyReports' => $salesReport,
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
            $salesReport = SalesReport::latest()->get();
            $company = Company::latest()->get();

            if ($request->start_date !== null && $request->End_date !== null && $request->company_id !== null) {
                $data = $salesReportQueryService->getMonthlyReportForTable($request->start_date, $request->end_date, $request->company_id);
    
                return view('admin.pos.sales_report.monthly.index', [
                    'companies' => $company,
                    'monthlyReports' => $data
                ]);
            }

            return view('admin.pos.sales_report.monthly.index', [
                'companies' => $company,
                'monthlyReports' => $salesReport
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
                'companies' => Company::get()
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
            $data = $salesReportQueryService->getDailyDetailReport(now()->toDateString());

            return view('admin.pos.sales_report.daily.show', [
                'salesReport' => $data
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Display the specified resource in Weekly.
     */
    public function showWeekly(SalesReport $salesReport, SalesReportQueryService $salesReportQueryService)
    {
        //
    }

    /**
     * Display the specified resource in monthly.
     */
    public function showMonthly(SalesReport $salesReport, SalesReportQueryService $salesReportQueryService)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalesReport $salesReport)
    {
        try {
            return view('admin.pos.sales_report.daily.edit', [
                'companies' => Company::get(),
                'report' => $salesReport->find(request()->segment(3))
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
            $salesReportService->update($salesReport->find(request()->segment(3)), $request->validated());

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
            $salesReportService->destroy($salesReport->find(request()->segment(3)));

            return redirect()->back()->with('success', 'Report Deleted!');
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }
}
