<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Stock;
use App\Models\StockMovement;
use Carbon\Carbon;

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
     * Get total number of product variants with low stock (current_stock <= 5).
     *
     * @return int
     */
    public function countLowStock(): int
    {
        return Stock::where('current_stock', '<=', 5)->count();
    }

    /**
     * Get total number of incoming stock transactions (qty > 0) in current month.
     *
     * @return int
     */
    public function countIncomingTransactions(): int
    {
        return StockMovement::where('qty', '>', 0)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
    }

    /**
     * Get total number of outgoing stock transactions (qty < 0) in current month.
     *
     * @return int
     */
    public function countOutgoingTransactions(): int
    {
        return StockMovement::where('qty', '<', 0)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
    }

    /**
     * Get paginated stock movements with eager loading.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getStockMovements()
    {
        return StockMovement::with(['productVariant.product', 'user'])
            ->latest()
            ->paginate(15);
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
            'low_stock_count' => $this->countLowStock(),
            'incoming_transactions'   => $this->countIncomingTransactions(),
            'outgoing_transactions'   => $this->countOutgoingTransactions(),
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
