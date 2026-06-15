<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Requests\ProductVariantStoreRequest;
use App\Http\Requests\ProductVariantUpdateRequest;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Unit;
use App\Services\ProductService;
use App\Services\ProductVariantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
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
    public function index(Request $request, ProductService $productService)
    {
        try {
            $keyword = $request->query('search', '');

            $products = $productService->search($keyword)
                ->load(['company', 'category', 'unit']);

            return view('admin.inventory.product.index', [
                'products' => $products,
                'search' => $keyword,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return view('admin.inventory.product.create', [
                'companies' => Company::query()->latest('id')->get(),
                'categories' => Category::query()->latest('id')->get(),
                'units' => Unit::query()->latest('id')->get(),
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request, ProductService $productService)
    {
        try {
            $productService->store($request->validated());

            return redirect()->route('inventory.product.index')->with('success', 'Product created successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        try {
            $product->load(['company', 'category', 'unit', 'variants' => function ($query) {
                $query->latest();
            }]);

            return view('admin.inventory.product.show', [
                'product' => $product,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        try {
            return view('admin.inventory.product.edit', [
                'product' => $product,
                'companies' => Company::query()->latest('id')->get(),
                'categories' => Category::query()->latest('id')->get(),
                'units' => Unit::query()->latest('id')->get(),
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductUpdateRequest $request, Product $product, ProductService $productService)
    {
        try {
            $productService->update($product, $request->validated());

            return redirect()->route('inventory.product.index')->with('success', 'Product updated successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, ProductService $productService)
    {
        try {
            $productService->destroy($product);

            return redirect()->route('inventory.product.index')->with('success', 'Product deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Store a newly created product variant in storage.
     *
     * @param  \App\Http\Requests\ProductVariantStoreRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeVariant(ProductVariantStoreRequest $request, Product $product, ProductVariantService $productVariantService)
    {
        $productVariantService->store($product, $request->validated(), $request->file('image'));

        return redirect()->back()->with('variant_success', 'Variant added successfully.');
    }

    /**
     * Update the specified product variant in storage.
     *
     * @param  \App\Http\Requests\ProductVariantUpdateRequest  $request
     * @param  \App\Models\Product  $product
     * @param  \App\Models\ProductVariant  $variant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateVariant(ProductVariantUpdateRequest $request, Product $product, ProductVariant $variant, ProductVariantService $productVariantService)
    {
        $productVariantService->update($variant, $request->validated(), $request->file('image'));

        return redirect()->back()->with('variant_success', 'Variant updated successfully.');
    }

    /**
     * Remove the specified product variant from storage.
     *
     * @param  \App\Models\Product  $product
     * @param  \App\Models\ProductVariant  $variant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyVariant(Product $product, ProductVariant $variant, ProductVariantService $productVariantService)
    {
        $productVariantService->destroy($variant);

        return redirect()->back()->with('variant_success', 'Variant deleted successfully.');
    }
}
