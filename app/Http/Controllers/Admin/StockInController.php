<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StockMovementTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StockInStoreRequest;
use App\Http\Requests\StockInUpdateRequest;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Services\StockInService;
use Illuminate\Http\Request;

class StockInController extends Controller
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
        $this->accessPage = $this->get_access_per_page('Incoming Good');

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
                $stockIns = StockMovement::with(['productVariant.product', 'user'])
                    ->whereIn('movement_type', ['purchase', 'opening'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);

                return view('admin.inventory.stock-in.index', compact('stockIns'));
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
                $allTypes = \App\Enums\StockMovementTypeEnum::labels();
                $movementTypes = array_filter($allTypes, function ($key) {
                    return in_array($key, ['opening', 'purchase', 'transfer']);
                }, ARRAY_FILTER_USE_KEY);

                return view('admin.inventory.stock-in.create', compact('productVariants', 'movementTypes'));
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StockInStoreRequest $request, StockInService $stockInService)
    {
        $access = $this->get_access();

        if (!isset($access['Create']) || $access['Create'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                $stockInService->store($request->validated());
                return redirect()->route('inventory.stock-in.index')
                    ->with('success', 'Barang masuk berhasil ditambahkan.');
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
                $allTypes = \App\Enums\StockMovementTypeEnum::labels();
                $movementTypes = array_filter($allTypes, function ($key) {
                    return in_array($key, ['opening', 'purchase', 'transfer']);
                }, ARRAY_FILTER_USE_KEY);

                return view('admin.inventory.stock-in.edit', compact('stockIn', 'productVariants', 'movementTypes'));
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StockInUpdateRequest $request, StockMovement $stockMovement, StockInService $stockInService)
    {
        $access = $this->get_access();

        if (!isset($access['Update']) || $access['Update'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                $stockInService->update($stockMovement, $request->validated());
                return redirect()->route('inventory.stock-in.index')
                    ->with('success', 'Barang masuk berhasil diperbarui.');
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockMovement $stockMovement, StockInService $stockInService)
    {
        $access = $this->get_access();

        if (!isset($access['Delete']) || $access['Delete'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                $stockInService->destroy($stockMovement);
                return redirect()->route('inventory.stock-in.index')
                    ->with('success', 'Barang masuk berhasil dihapus.');
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }
}
