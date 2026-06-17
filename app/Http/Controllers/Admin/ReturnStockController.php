<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StockMovementTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReturnStockStoreRequest;
use App\Http\Requests\ReturnStockUpdateRequest;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Services\ReturnStockService;
use Illuminate\Http\Request;

class ReturnStockController extends Controller
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
        $this->accessPage = $this->get_access_per_page('Return Item');

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
                $movements = StockMovement::with(['productVariant.product', 'user'])
                    ->where('movement_type', StockMovementTypeEnum::RETURN)
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);

                return view('admin.inventory.return-stock.index', compact('movements'));
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
                $productVariants = ProductVariant::with('product')->get();
                return view('admin.inventory.return-stock.create', compact('productVariants'));
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReturnStockStoreRequest $request, ReturnStockService $returnStockService)
    {
        $access = $this->get_access();

        if (!isset($access['Create']) || $access['Create'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                $returnStockService->store($request->validated());
                return redirect()->route('inventory.return-stock.index')
                    ->with('success', 'Retur barang berhasil dicatat.');
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StockMovement $stockMovement)
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
    public function edit(StockMovement $stockMovement)
    {
        $access = $this->get_access();

        if (!isset($access['Update']) || $access['Update'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                $productVariants = ProductVariant::with('product')->get();
                return view('admin.inventory.return-stock.edit', ['return' => $stockMovement, 'productVariants' => $productVariants]);
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReturnStockUpdateRequest $request, StockMovement $stockMovement, ReturnStockService $returnStockService)
    {
        $access = $this->get_access();

        if (!isset($access['Update']) || $access['Update'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                $returnStockService->update($stockMovement, $request->validated());
                return redirect()->route('inventory.return-stock.index')
                    ->with('success', 'Retur barang berhasil diperbarui.');
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockMovement $stockMovement, ReturnStockService $returnStockService)
    {
        $access = $this->get_access();

        if (!isset($access['Delete']) || $access['Delete'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                $returnStockService->destroy($stockMovement);
                return redirect()->route('inventory.return-stock.index')
                    ->with('success', 'Retur barang berhasil dihapus dan stok dikembalikan.');
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }
}
