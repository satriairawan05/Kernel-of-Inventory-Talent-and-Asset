<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Stock;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

    /**
     * Get access permissions for all pages within a specific module.
     *
     * Returns an array where keys are page names, values are arrays of action => access (0 or 1).
     * All standard CRUD actions (Create, Read, Update, Delete) are always present.
     *
     * @param string $module
     * @param int $groupId
     * @param string|null $pageName
     * @return array
     */
    public function getAccessByModule(string $module, int $groupId, ?string $pageName = null): array
    {
        // Query untuk mengambil semua action + access per halaman
        $query = DB::table('group_pages')
            ->join('pages', 'pages.id', '=', 'group_pages.page_id')
            ->where('pages.module', $module)
            ->where('group_pages.group_id', $groupId);

        if ($pageName !== null) {
            $query->where('pages.page_name', $pageName);
        }

        $pages = $query->select('pages.module','pages.page_name', 'pages.action', 'group_pages.access')->get();

        // Definisikan semua action yang mungkin (CRUD)
        $allActions = ['Create', 'Read', 'Update', 'Delete'];

        // Bangun hasil akhir
        $result = [];

        // Kelompokkan data per page_name
        $grouped = $pages->groupBy('page_name');

        foreach ($grouped as $pageName => $actions) {
            // Inisialisasi semua action dengan 0
            $accessMap = array_fill_keys($allActions, 0);

            // Isi access yang tersedia dari database
            foreach ($actions as $action) {
                if (in_array($action->action, $allActions)) {
                    $accessMap[$action->action] = (int) $action->access;
                }
            }

            $result[$pageName] = $accessMap;
        }

        // Jika pageName spesifik dan tidak ada data, tetap kembalikan array dengan semua action 0
        if ($pageName !== null && !isset($result[$pageName])) {
            $result[$pageName] = array_fill_keys($allActions, 0);
        }

        return $result;
    }

     /**
     * Get access for all modules (optional, if you want to load all at once)
     */
    public function getAllAccess(int $groupId): array
    {
        $modules = DB::table('pages')->distinct()->pluck('module')->toArray();
        $result = [];

        foreach ($modules as $module) {
            $result[$module] = $this->getAccessByModule($module, $groupId);
        }

        return $result;
    }
}
