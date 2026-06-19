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
    /*
    * Global Variable for Access Page
    */
    public $accessPage = [];

    /*
    * Get Access for Controller
    */
    public function get_access()
    {
        $this->accessPage = $this->get_access_per_page('Category');

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
                $categories = Category::paginate(10);
                $companies = Company::all();
    
                return view('admin.inventory.category.index', ['categories' => $categories, 'companies' => $companies, 'access' => $this->get_access_per_page('Category')]);
            } catch (QueryException $e) {
                Log::error($e->getMessage());
    
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
            } catch (QueryException $e) {
                Log::error($e->getMessage());
    
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryStoreRequest $request, CategoryService $categoryService)
    {
        $access = $this->get_access();

        if (!isset($access['Create']) || $access['Create'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                $categoryService->store($request->validated());
    
                return redirect()->route('inventory.category.index')->with('success', 'Category created successfully.');
            } catch (QueryException $e) {
                Log::error($e->getMessage());
    
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
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
    public function edit(Category $category)
    {
        $access = $this->get_access();

        if (!isset($access['Update']) || $access['Update'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                //
            } catch (QueryException $e) {
                Log::error($e->getMessage());
    
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryUpdateRequest $request, Category $category, CategoryService $categoryService)
    {
        $access = $this->get_access();

        if (!isset($access['Update']) || $access['Update'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                $categoryService->update($category, $request->validated());
    
                return redirect()->route('inventory.category.index')->with('success', 'Category updated successfully.');
    
            } catch (QueryException $e) {
                Log::error($e->getMessage());
    
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category, CategoryService $categoryService)
    {
        $access = $this->get_access();

        if (!isset($access['Delete']) || $access['Delete'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                $categoryService->destroy($category->find(request()->segment(3)));
    
                return redirect()->route('inventory.category.index')->with('success', 'Category deleted successfully.');
            } catch (QueryException $e) {
                Log::error($e->getMessage());
    
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }
}