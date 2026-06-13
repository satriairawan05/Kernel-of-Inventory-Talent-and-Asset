<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Category;
use App\Models\Company;
use App\Services\CategoryService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
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
            $categories = Category::paginate(10);

            return view('admin.inventory.category.index', ['categories' => $categories]);
        } catch (QueryException $e) {
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
            $companies = Company::all();

            return view('admin.inventory.category.create', ['companies' => $companies]);
        } catch (QueryException $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryStoreRequest $request, CategoryService $categoryService)
    {
        try {
            $categoryService->store($request->validated());

            return redirect()->route('inventory.category.index')->with('success', 'Category created successfully.');
        } catch (QueryException $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        try {
            //
        } catch (QueryException $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        try {
            $companies = Company::all();

            return view('admin.inventory.category.edit', ['category' => $category, 'companies' => $companies]);
        } catch (QueryException $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryUpdateRequest $request, Category $category, CategoryService $categoryService)
    {
        try {
            $categoryService->update($category, $request->validated());

            return redirect()->route('inventory.category.index')->with('success', 'Category updated successfully.');

        } catch (QueryException $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category, CategoryService $categoryService)
    {
        try {
            $categoryService->destroy($category->find(request()->segment(3)));

            return redirect()->route('inventory.category.index')->with('success', 'Category deleted successfully.');
        } catch (QueryException $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('failed', $e->getMessage());
        }
    }
}