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

    /**
     * Get the profile data of the currently authenticated user.
     *
     * This method retrieves the authenticated user's details from the users table
     * using the ID of the currently logged-in user obtained via Laravel's Auth facade.
     * It returns the User model instance or null if no user is authenticated.
     *
     * @return \App\Models\User|null
     */
    public function getProfile(): ?\App\Models\User
    {
        return \App\Models\User::find(auth()->user()->id);
    }
}
