<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockOpnameDetailUpdateRequest;
use App\Http\Requests\StockOpnamePeriodStoreRequest;
use App\Http\Requests\StockOpnamePeriodUpdateRequest;
use App\Models\ProductVariant;
use App\Models\StockOpnameDetail;
use App\Models\StockOpnamePeriod;
use App\Services\StockOpnameService;
use Illuminate\Http\RedirectResponse;

class StockOpnameController extends Controller
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
        $this->accessPage = $this->get_access_per_page('Stock Opname');

        $data = [
            "Create" => (int) $this->accessPage['Create'],
            "Read" => (int) $this->accessPage['Read'],
            "Update" => (int) $this->accessPage['Update'],
            "Delete" => (int) $this->accessPage['Delete'],
        ];

        return $data;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(StockOpnameService $stockOpnameService)
    {
        $access = $this->get_access();

        if (!isset($access['Read']) || $access['Read'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                $productVariants = ProductVariant::with('product')->get();
                $periods = $stockOpnameService->getPeriods();

                return view('admin.inventory.stock-opname.index', compact('periods','productVariants', 'access'));
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $access = $this->get_access();

        if (!isset($access['Create']) || $access['Create'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                //
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StockOpnamePeriodStoreRequest $request, StockOpnameService $stockOpnameService)
    {
        $access = $this->get_access();

        if (!isset($access['Create']) || $access['Create'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                $validated = $request->validated();
                $details = $validated['details'];
                unset($validated['details']);

                $stockOpnameService->storePeriod($validated, $details);

                return redirect()->route('inventory.stock-opname.index')->with('success', 'Periode opname berhasil dibuat.');
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StockOpnameService $stockOpnameService, StockOpnamePeriod $stockOpnamePeriod)
    {
        $access = $this->get_access();

        if (!isset($access['Read']) || $access['Read'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                $period = $stockOpnameService->getPeriodWithDetails($stockOpnamePeriod);
                return view('admin.inventory.stock-opname.show', compact('period'));
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        $access = $this->get_access();

        if (!isset($access['Update']) || $access['Update'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                //
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }

    /**
     * Update a single detail (physical stock).
     */
    public function updateDetail(StockOpnameDetailUpdateRequest $request, StockOpnameDetail $stockOpnameDetail, StockOpnameService $stockOpnameService): RedirectResponse
    {
        $stockOpnameService->updateDetail($stockOpnameDetail, $request->validated());

        return redirect()->back()->with('success', 'Stok fisik berhasil diperbarui.');
    }

    /**
     * Close period (set status closed).
     */
    public function close(StockOpnamePeriod $stockOpnamePeriod, StockOpnameService $stockOpnameService): RedirectResponse
    {
        $stockOpnameService->closePeriod($stockOpnamePeriod);

        return redirect()->route('inventory.stock-opname.index')
            ->with('success', 'Periode opname ditutup.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StockOpnamePeriodUpdateRequest $request, StockOpnameService $stockOpnameService)
    {
        $access = $this->get_access();

        if (!isset($access['Update']) || $access['Update'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                //
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockOpnameService $stockOpnameService)
    {
        $access = $this->get_access();

        if (!isset($access['Delete']) || $access['Delete'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                //
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }
}
