<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockStoreRequest;
use App\Http\Requests\StockUpdateRequest;
use App\Models\ProductVariant;
use App\Models\Stock;
use App\Services\ModuleService;
use App\Services\StockService;

class StockController extends Controller
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
        $this->accessPage = $this->get_access_per_page('Stock');

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
    public function index()
    {
        $access = $this->get_access();

        if (!isset($access['Read']) || $access['Read'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                $stocks = Stock::with('productVariant.product')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
                $productVariants = ProductVariant::with('product')->get();
    
                return view('admin.inventory.stock.index', ['stocks' => $stocks, 'access' => $this->get_access_per_page('Stock'), 'productVariants' => $productVariants]);
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
    public function store(StockStoreRequest $request, StockService $stockService)
    {
        $access = $this->get_access();

        if (!isset($access['Create']) || $access['Create'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                $stockService->store($request->validated());
    
                return redirect()
                    ->route('inventory.stock.index')
                    ->with('success', 'Stock Added Successfully!.');
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Stock $stock)
    {
        $access = $this->get_access();

        if (!isset($access['Read']) || $access['Read'] != 1) {
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
     * Show the form for editing the specified resource.
     */
    public function edit(Stock $stock)
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
     * Update the specified resource in storage.
     */
    public function update(StockUpdateRequest $request, Stock $stock, StockService $stockService)
    {
        $access = $this->get_access();

        if (!isset($access['Update']) || $access['Update'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                $stockService->update($stock, $request->validated());
    
                return redirect()
                    ->route('inventory.stock.index')
                    ->with('success', 'Stock Updated Successfully!.');
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stock $stock, StockService $stockService)
    {
        $access = $this->get_access();

        if (!isset($access['Read']) || $access['Read'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                $stockService->destroy($stock);
    
                return redirect()
                    ->route('inventory.stock.index')
                    ->with('success', 'Stock Deleted Successfully!.');
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
        
    }

    /*
    * Get Access for Controller
    */
    public function get_access_log()
    {
        $this->accessPage = $this->get_access_per_page('Log');

        $data = [
            "Read" => (int) $this->accessPage['Read'],
        ];

        return $data;
    }


    /*
    * Get Access for Stock Log
    */
    public function stockLogs(ModuleService $moduleService)
    {
        $access = $this->get_access();

        if (!isset($access['Read']) || $access['Read'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            $movements = $moduleService->getStockMovements();
            return view('admin.inventory.log.stock', ['movements' => $movements,'access' => $this->get_access_per_page('Log')]);
        }
    }
}
