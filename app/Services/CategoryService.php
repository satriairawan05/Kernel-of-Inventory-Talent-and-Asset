<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    /**
     * Create a new category for a company.
     *
     * @param array $data
     * @return Category
     */
    public function store(array $data): Category
    {
        return DB::transaction(function () use ($data) {
            $company = Company::findOrFail($data['company_id']);

            return Category::create([
                'company_id' => $company->id,
                'category_name' => $data['category_name'],
                'description' => $data['description'],
            ]);
        });
    }

    /**
     * Update an existing category.
     *
     * @param Category $category
     * @param array $data
     * @return Category
     */
    public function update(Category $category, array $data): Category
    {
        return DB::transaction(function () use ($category, $data) {
            $category->update([
                'company_id' => $data['company_id'],
                'category_name' => $data['category_name'],
                'description' => $data['description'],
            ]);

            return $category->fresh();
        });
    }

    /**
     * Delete a category.
     *
     * @param Category $category
     * @return bool
     */
    public function destroy(Category $category): bool
    {
        return DB::transaction(function () use ($category) {
            return $category->delete();
        });
    }
}