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
        try {
            $stocks = Stock::with('productVariant.product')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('admin.inventory.stock.index', compact('stocks'));
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
            $productVariants = ProductVariant::with('product')->get();

            return view('admin.inventory.stock.create', ['productVariants' => $productVariants]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StockStoreRequest $request, StockService $stockService)
    {
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

    /**
     * Display the specified resource.
     */
    public function show(Stock $stock)
    {
        try {
            //
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stock $stock)
    {
        try {
            $productVariants = ProductVariant::with('product')->get();

            return view('admin.inventory.stock.edit', compact('stock', 'productVariants'));
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StockUpdateRequest $request, Stock $stock, StockService $stockService)
    {
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stock $stock, StockService $stockService)
    {
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

    public function stockLogs(ModuleService $moduleService)
    {
        $movements = $moduleService->getStockMovements();
        return view('admin.inventory.log.stock', ['movements' => $movements]);
    }
}
