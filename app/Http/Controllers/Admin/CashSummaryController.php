<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CashSummaryStoreRequest;
use App\Http\Requests\CashSummaryUpdateRequest;
use App\Models\CashSummary;
use App\Services\CashSummaryService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CashSummaryController extends Controller
{
    /*
    * Global Variable for Access Page
    */
    public $accessPage = [];

    /*
    * Get Access for Controller
    */
    public function get_access()
    {
        $this->accessPage = $this->get_access_per_page('Cash Summary');

        $data = [
            'Create' => (int) $this->accessPage['Create'],
            'Read'   => (int) $this->accessPage['Read'],
            'Update' => (int) $this->accessPage['Update'],
            'Delete' => (int) $this->accessPage['Delete'],
        ];

        return $data;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $access = $this->get_access();

        if (! isset($access['Read']) || $access['Read'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        }

        try {
            $user = auth()->user();
            $companyId = ($user->group_id == 1) ? 1 : $user->company_id;

            $filters = [
                'start_date' => $request->get('start_date', now()->startOfMonth()->toDateString()),
                'end_date'   => $request->get('end_date', now()->endOfMonth()->toDateString()),
                'type'       => $request->get('type'),
                'search'     => $request->get('search'),
            ];

            $cashSummaryService = new CashSummaryService();

            // Daily summary with pagination
            $dailySummaries = $cashSummaryService->getDailySummaryPaginated(
                $companyId,
                15,
                $filters['start_date'],
                $filters['end_date']
            );

            // Summary statistics
            $summary = $cashSummaryService->getSummary(
                $companyId,
                $filters['start_date'],
                $filters['end_date']
            );

            $types = \App\Enums\CashSummaryTypeEnum::options();

            return view('admin.pos.cash_summary.index', compact(
                'dailySummaries',
                'access',
                'summary',
                'types',
                'filters'
            ));
        } catch (\Exception $e) {
            Log::error('Cash summary index error: ' . $e->getMessage());
            return redirect()->back()->with('failed', 'Failed to load cash summary: ' . $e->getMessage());
        }
    }

    /**
     * Show detailed records for a specific date.
     */
    public function detail(Request $request, string $date)
    {
        $access = $this->get_access();

        if (! isset($access['Read']) || $access['Read'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        }

        try {
            $user = auth()->user();
            $companyId = ($user->group_id == 1) ? 1 : $user->company_id;

            $cashSummaryService = new CashSummaryService();
            $records = $cashSummaryService->getByDate($companyId, $date);
            $summary = $cashSummaryService->getSummary($companyId, $date, $date);

            return view('admin.pos.cash_summary.detail', compact(
                'records',
                'summary',
                'date',
                'access'
            ));
        } catch (\Exception $e) {
            Log::error('Cash summary detail error: ' . $e->getMessage());
            return redirect()->back()->with('failed', 'Failed to load detail: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CashSummaryStoreRequest $request, CashSummaryService $service)
    {
        $access = $this->get_access();

        if (! isset($access['Create']) || $access['Create'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        }

        try {
            $data = $request->validated();

            // Set company_id: use user's company, or default to 1 if admin without company
            $user = auth()->user();
            $data['company_id'] = $user->company_id ?? 1;

            $service->store($data);

            return redirect()->route('pos.cash_summary.index')
                ->with('success', 'Cash record created successfully.');
        } catch (QueryException $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CashSummary $cashSummary)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CashSummary $cashSummary)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CashSummaryUpdateRequest $request, CashSummary $cashSummary, CashSummaryService $service)
    {
        $access = $this->get_access();

        if (! isset($access['Update']) || $access['Update'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        }

        $user = auth()->user();

        // Verify ownership: admin can edit any, others only own company
        if ($user->group_id != 1 && $cashSummary->company_id !== $user->company_id) {
            return redirect()->back()->with('failed', "You don't have authority for this record.");
        }

        try {
            $service->update($cashSummary, $request->validated());

            return redirect()->route('pos.cash_summary.index')
                ->with('success', 'Cash record updated successfully.');
        } catch (QueryException $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CashSummary $cashSummary, CashSummaryService $service)
    {
        $access = $this->get_access();

        if (! isset($access['Delete']) || $access['Delete'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        }

        $user = auth()->user();

        // Verify ownership: admin can delete any, others only own company
        if ($user->group_id != 1 && $cashSummary->company_id !== $user->company_id) {
            return redirect()->back()->with('failed', "You don't have authority for this record.");
        }

        try {
            $service->destroy($cashSummary);

            return redirect()->route('pos.cash_summary.index')
                ->with('success', 'Cash record deleted successfully.');
        } catch (QueryException $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Remove all cash summaries.
     * Only accessible by admin (group_id == 1).
     */
    public function destroyAll(CashSummaryService $service)
    {
        $access = $this->get_access();

        if (! isset($access['Delete']) || $access['Delete'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        }

        $user = auth()->user();

        // Only admin can delete all
        if ($user->group_id != 1) {
            return redirect()->back()->with('failed', "You don't have authority to delete all records.");
        }

        try {
            // Admin can delete all records (all companies)
            $service->deleteAll();

            return redirect()->route('pos.cash_summary.index')
                ->with('success', 'All cash records deleted successfully.');
        } catch (QueryException $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }
}
