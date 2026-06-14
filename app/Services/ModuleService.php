<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;

class ModuleService
{
    /**
     * Get total number of products.
     *
     * @return int
     */
    public function countProduct(): int
    {
        return Product::count();
    }

    /**
     * Get total number of product variants.
     *
     * @return int
     */
    public function countProductVariant(): int
    {
        return ProductVariant::count();
    }

    /**
     * Get all inventory statistics for dashboard.
     *
     * @return array<string, int>
     */
    public function getInventoryStats(): array
    {
        return [
            'total_products' => $this->countProduct(),
            'total_variants' => $this->countProductVariant(),
        ];
    }
}