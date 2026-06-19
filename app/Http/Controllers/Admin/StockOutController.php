<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StockMovementTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StockOutStoreRequest;
use App\Http\Requests\StockOutUpdateRequest;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Services\StockOutService;
use Illuminate\Http\Request;

class StockOutController extends Controller
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
        $this->accessPage = $this->get_access_per_page('Exit Item');

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
                    ->where('movement_type', \App\Enums\StockMovementTypeEnum::SALE)
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
                $productVariants = ProductVariant::with('product')->get();
                $allTypes = StockMovementTypeEnum::labels();
                $allowedKeys = ['sale', 'adjustment', 'return'];
                $movementTypes = array_intersect_key($allTypes, array_flip($allowedKeys));

                return view('admin.inventory.stock-out.index', compact('movements','productVariants','movementTypes','access'));
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
                $allTypes = StockMovementTypeEnum::labels();
                $allowedKeys = ['sale', 'adjustment', 'return'];
                $movementTypes = array_intersect_key($allTypes, array_flip($allowedKeys));

                return view('admin.inventory.stock-out.create', compact('productVariants', 'movementTypes'));
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StockOutStoreRequest $request, StockOutService $stockOutService)
    {
        $access = $this->get_access();

        if (!isset($access['Create']) || $access['Create'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                $stockOutService->store($request->validated());
                return redirect()->route('inventory.stock-out.index')
                    ->with('success', 'Barang keluar berhasil dicatat.');
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
                $allTypes = StockMovementTypeEnum::labels();
                $allowedKeys = ['sale', 'adjustment', 'return'];
                $movementTypes = array_intersect_key($allTypes, array_flip($allowedKeys));

                return view('admin.inventory.stock-out.edit', compact('stockOut', 'productVariants', 'movementTypes'));
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StockOutUpdateRequest $request, StockMovement $stockMovement, StockOutService $stockOutService)
    {
        $access = $this->get_access();

        if (!isset($access['Update']) || $access['Update'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                $stockOutService->update($stockMovement, $request->validated());
                return redirect()->route('inventory.stock-out.index')
                    ->with('success', 'Barang keluar berhasil diperbarui.');
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockMovement $stockMovement, StockOutService $stockOutService)
    {
        $access = $this->get_access();

        if (!isset($access['Delete']) || $access['Delete'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                $stockOutService->destroy($stockMovement);
                return redirect()->route('inventory.stock-out.index')
                    ->with('success', 'Barang keluar berhasil dihapus dan stok dikembalikan.');
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }
}
