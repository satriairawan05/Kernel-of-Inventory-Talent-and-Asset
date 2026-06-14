<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Search products by name or code.
     */
    public function search(string $keyword = ''): Collection
    {
        $keyword = trim($keyword);

        $query = Product::query()->with(['variants']);

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('product_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('product_code', 'LIKE', '%' . $keyword . '%');
            });
        }

        return $query->orderBy('product_name')->get();
    }

    /**
     * Store a new product record.
     */
    public function store(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            return Product::create([
                'company_id'   => $data['company_id'] ?? null,
                'category_id'  => $data['category_id'] ?? null,
                'unit_id'      => $data['unit_id'] ?? null,
                'product_name' => $data['product_name'] ?? null,
                'product_code' => $data['product_code'] ?? null,
                'description'  => $data['description'] ?? null,
                'has_variant'  => $data['has_variant'] ?? false,
                'is_active'    => $data['is_active'] ?? true,
            ]);
        });
    }

    /**
     * Update an existing product record.
     */
    public function update(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $product->update([
                'company_id'   => $data['company_id'] ?? $product->company_id,
                'category_id'  => $data['category_id'] ?? $product->category_id,
                'unit_id'      => $data['unit_id'] ?? $product->unit_id,
                'product_name' => $data['product_name'] ?? $product->product_name,
                'product_code' => $data['product_code'] ?? $product->product_code,
                'description'  => $data['description'] ?? $product->description,
                'has_variant'  => $data['has_variant'] ?? $product->has_variant,
                'is_active'    => $data['is_active'] ?? $product->is_active,
            ]);

            return $product->fresh();
        });
    }

    /**
     * Delete a product record.
     */
    public function destroy(Product $product): bool
    {
        return DB::transaction(function () use ($product) {
            return $product->delete();
        });
    }
}
