<?php

namespace App\Http\Controllers\API;

use App\Enums\{DraftTypeEnum, DraftStatusEnum};
use App\Enums\MenuStatusEnum;
use App\Http\Controllers\Controller;
use App\Jobs\PrintReceiptJob;
use App\Services\CashierService;
use App\Models\CashierSession;
use App\Models\{Company, MenuItem, Shift, Draft, DraftItem, Cart, Stock, Transaction, TransactionItem};
use App\Models\InventoryReport;
use App\Models\InventoryReportItem;
use App\Models\ReportPeriod;
use App\Models\StockMovement;
use App\Services\PrintService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log, Storage};
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class APIController extends Controller
{
    /**
     * API Response helper.
     */
    private function response($data = null, $message = 'Success', $status = 200)
    {
        return response()->json([
            'success' => $status >= 200 && $status < 300,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    /**
     * Get authenticated user or fallback for development.
     */
    private function getUser(Request $request)
    {
        $user = auth()->user();
        if ($user) {
            return $user;
        }

        // Fallback untuk development (local) - jika tidak ada auth
        if (app()->environment('local')) {
            $userId = $request->input('user_id', 1);
            $companyId = $request->input('company_id', 1);
            return (object) [
                'id'         => $userId,
                'company_id' => $companyId,
                'group_id'   => 1, // admin
                'name'       => 'Development User',
            ];
        }

        return null;
    }

    /**
     * Refresh CSRF token (web route, uses session).
     */
    public function refreshCsrfToken(Request $request)
    {
        $request->session()->regenerateToken();
        return response()->json(['csrf_token' => csrf_token()]);
    }

    /**
     * Get all companies/outlets.
     * GET /api/companies
     */
    public function getCompanies(Request $request)
    {
        try {
            $companies = Company::select('id', 'company_name as name')
                ->where('use_menu', true)
                ->orderBy('company_name')
                ->get();
            return $this->response($companies);
        } catch (\Exception $e) {
            Log::error('API getCompanies error: ' . $e->getMessage());
            return $this->response(null, 'Failed to fetch companies', 500);
        }
    }

    /**
     * Get shifts by company_id.
     * GET /api/shifts?company_id={id}
     */
    public function getShifts(Request $request)
    {
        try {
            $user = $this->getUser($request);
            $companyId = $request->has('company_id') ? $request->company_id : null;

            if ($user && $user->group_id != 1 && !$companyId) {
                $companyId = $user->company_id;
            }

            $query = Shift::select(
                'id',
                'company_id',
                'shift_name as name',
                'start_time as start',
                'end_time as end',
                'shift_code as code',
                'late_tolerance_minutes',
                'early_leave_tolerance_minutes'
            );

            if ($companyId) {
                $query->where('company_id', $companyId);
            }

            $shifts = $query->orderBy('start_time')->get();

            return $this->response($shifts);
        } catch (\Exception $e) {
            Log::error('API getShifts error: ' . $e->getMessage());
            return $this->response(null, 'Failed to fetch shifts', 500);
        }
    }

    /**
     * Get menu items for POS.
     * GET /api/menu?company_id={id}&category={cat}
     */
    public function getMenu(Request $request)
    {
        try {
            $user = $this->getUser($request);
            $companyId = $request->has('company_id') ? $request->company_id : ($user->company_id ?? 1);

            $query = MenuItem::with(['productVariant.stock'])
                ->when($companyId, function ($q) use ($companyId) {
                    return $q->where('company_id', $companyId);
                })
                ->orderBy('name');

            if ($request->has('category') && $request->category != 'all') {
                $query->where('category', $request->category);
            }

            $menu = $query->get();

            // If no menu items, return empty array
            if ($menu->isEmpty()) {
                return $this->response([]);
            }

            // Group menu items by product_variant_id
            $variantGroups = [];
            foreach ($menu as $item) {
                $variantId = $item->product_variant_id;
                if ($variantId) {
                    if (!isset($variantGroups[$variantId])) {
                        $variantGroups[$variantId] = [
                            'items'      => [],
                            'totalStock' => 0,
                        ];
                    }
                    $variantGroups[$variantId]['items'][] = $item;
                }
            }

            // Fetch total stock for each variant
            $variantIds = array_keys($variantGroups);
            if (!empty($variantIds)) {
                $stocks = Stock::whereIn('product_variant_id', $variantIds)->get()->keyBy('product_variant_id');
                foreach ($variantIds as $vid) {
                    // Safe access: check if stock exists
                    $variantGroups[$vid]['totalStock'] = (int) ($stocks[$vid]->current_stock ?? 0);
                }
            }

            // Calculate stock per menu and status for each item
            foreach ($variantGroups as $variantId => $group) {
                $totalStock = $group['totalStock'];
                $count = count($group['items']);
                $perMenuStock = $count > 0 ? floor($totalStock / $count) : 0;
                foreach ($group['items'] as $item) {
                    $item->per_menu_stock = $perMenuStock;
                    // Determine status based on perMenuStock
                    if ($perMenuStock <= 0) {
                        $item->calculated_status = 'out';
                    } elseif ($perMenuStock <= 25) {
                        $item->calculated_status = 'low';
                    } else {
                        $item->calculated_status = 'available';
                    }
                }
            }

            // For items without variant, use stock & status from database
            foreach ($menu as $item) {
                if (!$item->product_variant_id) {
                    $item->per_menu_stock = (int) ($item->stock ?? 0);
                    $item->calculated_status = $item->status ?? 'available';
                }
            }

            $formatted = $menu->map(function ($item) {
                $imageUrl = null;
                if (method_exists($item, 'getImageUrlAttribute')) {
                    $imageUrl = $item->image_url;
                } elseif ($item->image) {
                    $imageUrl = asset('storage/' . $item->image);
                }

                // Safely get variant info
                $variantData = null;
                if ($item->productVariant) {
                    $variantData = [
                        'id'    => $item->productVariant->id,
                        'name'  => $item->productVariant->variant_name ?? '',
                        'stock' => (int) ($item->productVariant->stock?->current_stock ?? 0),
                    ];
                }

                return [
                    'id'       => $item->id,
                    'name'     => $item->name,
                    'price'    => (int) $item->price,
                    'category' => $item->category,
                    'status'   => $item->calculated_status ?? $item->status ?? 'available',
                    'image'    => $imageUrl,
                    'stock'    => (int) ($item->per_menu_stock ?? $item->stock ?? 0),
                    'icon'     => $this->getInitials($item->name),
                    'variant'  => $variantData,
                ];
            });

            return $this->response($formatted);
        } catch (\Exception $e) {
            // Log detailed error for debugging
            Log::error('API getMenu error: ' . $e->getMessage());
            Log::error('API getMenu trace: ' . $e->getTraceAsString());
            return $this->response(null, 'Failed to fetch menu: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Create a new menu item.
     * POST /api/menu
     */
    public function storeMenu(Request $request)
    {
        try {
            $user = $this->getUser($request);
            if (!$user) {
                return $this->response(null, 'Unauthorized', 401);
            }

            $companyId = $request->input('company_id', $user->company_id ?? 1);

            $validated = $request->validate([
                'name'     => 'required|string|max:255',
                'price'    => 'required|numeric|min:0',
                'category' => 'required|in:food,drink,snack,additional',
                'status'   => 'required|in:available,low,out',
                'image'    => 'nullable|image|max:2048',
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('menu_images', 'public');
            }

            $menu = MenuItem::create([
                'company_id'         => $companyId,
                'product_variant_id' => null,
                'name'               => $validated['name'],
                'price'              => $validated['price'],
                'category'           => $validated['category'],
                'status'             => $validated['status'],
                'image'              => $imagePath,
                'stock'              => $validated['category'] === 'additional' ? null : 0,
            ]);

            $menu->refresh();

            return $this->response([
                'id'       => $menu->id,
                'name'     => $menu->name,
                'price'    => (int) $menu->price,
                'category' => $menu->category,
                'status'   => $menu->status,
                'image'    => $menu->image ? asset('storage/' . $menu->image) : null,
            ], 'Menu created successfully', 201);
        } catch (\Exception $e) {
            Log::error('API storeMenu error: ' . $e->getMessage());
            return $this->response(null, 'Failed to create menu: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update a menu item.
     * PUT /api/menu/{id}
     */
    public function updateMenu(Request $request, $id)
    {
        try {
            $user = $this->getUser($request);
            if (!$user || $user->group_id != 1) {
                return $this->response(null, 'Unauthorized. Only admin can manage menu.', 403);
            }

            $companyId = $user->company_id ?? 1;

            $menu = MenuItem::where('company_id', $companyId)->find($id);
            if (!$menu) {
                return $this->response(null, 'Menu item not found', 404);
            }

            $validated = $request->validate([
                'name'     => 'required|string|max:255',
                'price'    => 'required|numeric|min:0',
                'category' => 'required|in:food,drink,snack,additional',
                'status'   => 'required|in:available,low,out',
                'image'    => 'nullable|image|max:2048',
            ]);

            $menu->name = $validated['name'];
            $menu->price = $validated['price'];
            $menu->category = $validated['category'];
            $menu->status = $validated['status'];

            if ($request->hasFile('image')) {
                if ($menu->image && Storage::disk('public')->exists($menu->image)) {
                    Storage::disk('public')->delete($menu->image);
                }
                $path = $request->file('image')->store('menu_images', 'public');
                $menu->image = $path;
            }

            $menu->save();
            $menu->refresh();

            return $this->response([
                'id'       => $menu->id,
                'name'     => $menu->name,
                'price'    => (int) $menu->price,
                'category' => $menu->category,
                'status'   => $menu->status,
                'image'    => $menu->image ? asset('storage/' . $menu->image) : null,
            ], 'Menu updated successfully');
        } catch (\Exception $e) {
            Log::error('API updateMenu error: ' . $e->getMessage());
            return $this->response(null, 'Failed to update menu: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete a menu item.
     * DELETE /api/menu/{id}
     */
    public function deleteMenu($id)
    {
        try {
            $user = $this->getUser(request());
            if (!$user || $user->group_id != 1) {
                return $this->response(null, 'Unauthorized. Only admin can manage menu.', 403);
            }

            $companyId = $user->company_id ?? 1;

            $menu = MenuItem::where('company_id', $companyId)->find($id);
            if (!$menu) {
                return $this->response(null, 'Menu item not found', 404);
            }

            if ($menu->image && Storage::disk('public')->exists($menu->image)) {
                Storage::disk('public')->delete($menu->image);
            }

            $menu->delete();

            return $this->response(null, 'Menu deleted successfully');
        } catch (\Exception $e) {
            Log::error('API deleteMenu error: ' . $e->getMessage());
            return $this->response(null, 'Failed to delete menu: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get initials from name.
     */
    private function getInitials($name)
    {
        if (empty($name)) return '??';
        $words = explode(' ', $name);
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        if (strlen($initials) < 2) {
            $initials = strtoupper(substr($name, 0, 2));
        }
        return $initials;
    }

    // ============================================================
    // DRAFT MANAGEMENT
    // ============================================================

    /**
     * Get all drafts for a company (active & processing).
     * GET /api/drafts?company_id={id}
     */
    public function getDrafts(Request $request)
    {
        try {
            $user = $this->getUser($request);
            $companyId = $request->has('company_id')
                ? $request->company_id
                : ($user->company_id ?? 1);

            $drafts = Draft::with('items')
                ->where('company_id', $companyId)
                ->whereIn('status', [DraftStatusEnum::ACTIVE->value, DraftStatusEnum::PROCESSING->value])
                ->orderBy('created_at', 'desc')
                ->get();

            $formatted = $drafts->map(function ($draft) {
                return [
                    'id'          => $draft->id,
                    'name'        => $draft->name,
                    'type'        => $draft->type,
                    'typeLabel'   => $draft->type === 'dinein' ? '🍽️ Dine In' : '🛍️ Take Away',
                    'table'       => $draft->table_number,
                    'subtotal'    => (float) $draft->subtotal,
                    'status'      => $draft->status,
                    'items'       => $draft->items->map(function ($item) {
                        return [
                            'id'    => $item->id,
                            'menu_item_id' => $item->menu_item_id,
                            'name'  => $item->name,
                            'price' => (float) $item->price,
                            'qty'   => $item->qty,
                            'total' => (float) $item->total,
                        ];
                    }),
                    'createdAt'   => $draft->created_at->toISOString(),
                ];
            });

            return $this->response($formatted);
        } catch (\Exception $e) {
            Log::error('API getDrafts error: ' . $e->getMessage());
            return $this->response(null, 'Failed to fetch drafts', 500);
        }
    }

    /**
     * Create a new draft with items (optional).
     * POST /api/drafts
     */
    public function createDraft(Request $request)
    {
        try {
            $user = $this->getUser($request);
            if (!$user) {
                return $this->response(null, 'Unauthorized', 401);
            }

            $companyId = $request->input('company_id', $user->company_id ?? 1);

            $validated = $request->validate([
                'type'        => ['required', Rule::in(DraftTypeEnum::values())],
                'table_number' => 'nullable|integer|min:1',
                'name'        => 'required|string|max:255',
                'items'       => 'nullable|array',
                'items.*.menu_item_id' => 'nullable|exists:menu_items,id',
                'items.*.name'        => 'required|string|max:255',
                'items.*.price'       => 'required|numeric|min:0',
                'items.*.qty'         => 'required|integer|min:1',
            ]);

            DB::beginTransaction();

            $draft = Draft::create([
                'company_id'   => $companyId,
                'type'         => $validated['type'],
                'table_number' => $validated['table_number'] ?? null,
                'name'         => $validated['name'],
                'status'       => DraftStatusEnum::ACTIVE,
                'subtotal'     => 0,
            ]);

            $subtotal = 0;
            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $itemData) {
                    $total = $itemData['price'] * $itemData['qty'];
                    DraftItem::create([
                        'draft_id'     => $draft->id,
                        'menu_item_id' => $itemData['menu_item_id'] ?? null,
                        'name'         => $itemData['name'],
                        'price'        => $itemData['price'],
                        'qty'          => $itemData['qty'],
                        'total'        => $total,
                    ]);
                    $subtotal += $total;
                }
                $draft->subtotal = $subtotal;
                $draft->save();
            }

            DB::commit();
            $draft->load('items');

            return $this->response([
                'id'          => $draft->id,
                'name'        => $draft->name,
                'type'        => $draft->type,
                'typeLabel'   => $draft->type === 'dinein' ? '🍽️ Dine In' : '🛍️ Take Away',
                'table'       => $draft->table_number,
                'subtotal'    => (float) $draft->subtotal,
                'status'      => $draft->status,
                'items'       => $draft->items->map(function ($item) {
                    return [
                        'id'    => $item->id,
                        'menu_item_id' => $item->menu_item_id,
                        'name'  => $item->name,
                        'price' => (float) $item->price,
                        'qty'   => $item->qty,
                        'total' => (float) $item->total,
                    ];
                }),
                'createdAt'   => $draft->created_at->toISOString(),
            ], 'Draft created successfully', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API createDraft error: ' . $e->getMessage());
            return $this->response(null, 'Failed to create draft: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete a draft (only if status is active or processing).
     * DELETE /api/drafts/{id}
     */
    public function deleteDraft($id)
    {
        try {
            $user = $this->getUser(request());
            if (!$user) {
                return $this->response(null, 'Unauthorized', 401);
            }

            $companyId = $user->company_id ?? 1;

            $draft = Draft::where('company_id', $companyId)->find($id);
            if (!$draft) {
                return $this->response(null, 'Draft not found', 404);
            }

            if ($draft->status === DraftStatusEnum::COMPLETED) {
                return $this->response(null, 'Cannot delete completed draft', 403);
            }

            $draft->delete();
            return $this->response(null, 'Draft deleted successfully', 200);
        } catch (\Exception $e) {
            Log::error('API deleteDraft error: ' . $e->getMessage());
            return $this->response(null, 'Failed to delete draft', 500);
        }
    }

    /**
     * Activate a draft (change status from processing to active).
     * POST /api/drafts/{id}/activate
     */
    public function activateDraft($id)
    {
        try {
            $user = $this->getUser(request());
            if (!$user) {
                return $this->response(null, 'Unauthorized', 401);
            }

            $companyId = $user->company_id ?? 1;

            $draft = Draft::where('company_id', $companyId)->find($id);
            if (!$draft) {
                return $this->response(null, 'Draft not found', 404);
            }

            if ($draft->status === DraftStatusEnum::COMPLETED) {
                return $this->response(null, 'Cannot activate completed draft', 403);
            }

            $draft->status = DraftStatusEnum::ACTIVE;
            $draft->save();

            return $this->response([
                'id'     => $draft->id,
                'status' => $draft->status,
            ], 'Draft activated successfully');
        } catch (\Exception $e) {
            Log::error('API activateDraft error: ' . $e->getMessage());
            return $this->response(null, 'Failed to activate draft', 500);
        }
    }

    // ============================================================
    // CART MANAGEMENT
    // ============================================================

    /**
     * Get active cart for current user.
     * GET /api/cart
     */
    public function getCart(Request $request)
    {
        try {
            $user = $this->getUser($request);
            if (!$user) {
                return $this->response(null, 'Unauthorized', 401);
            }

            $companyId = $user->company_id ?? 1;

            $cart = Cart::where('company_id', $companyId)
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->with('items')
                ->first();

            if (!$cart) {
                return $this->response(null, 'No active cart', 200);
            }

            return $this->response($cart);
        } catch (\Exception $e) {
            Log::error('API getCart error: ' . $e->getMessage());
            return $this->response(null, 'Failed to get cart', 500);
        }
    }

    /**
     * Create a new cart for the user.
     * POST /api/cart
     */
    public function createCart(Request $request)
    {
        try {
            $user = $this->getUser($request);
            if (!$user) {
                return $this->response(null, 'Unauthorized', 401);
            }

            $companyId = $user->company_id ?? 1;

            $validated = $request->validate([
                'type' => 'required|in:dinein,takeaway',
                'table_number' => 'nullable|integer|min:1',
            ]);

            $existing = Cart::where('company_id', $companyId)
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if ($existing) {
                return $this->response($existing->load('items'), 'Existing cart found');
            }

            $cart = Cart::create([
                'company_id' => $companyId,
                'user_id' => $user->id,
                'type' => $validated['type'],
                'table_number' => $validated['table_number'] ?? null,
                'status' => 'active',
                'subtotal' => 0,
                'total' => 0,
            ]);

            return $this->response($cart->load('items'), 'Cart created successfully', 201);
        } catch (\Exception $e) {
            Log::error('API createCart error: ' . $e->getMessage());
            return $this->response(null, 'Failed to create cart: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update cart (type, table_number, notes).
     * PUT /api/cart/{id}
     */
    public function updateCart(Request $request, $id)
    {
        try {
            $user = $this->getUser($request);
            if (!$user) {
                return $this->response(null, 'Unauthorized', 401);
            }

            $companyId = $user->company_id ?? 1;

            $cart = Cart::where('company_id', $companyId)
                ->where('user_id', $user->id)
                ->find($id);

            if (!$cart) {
                return $this->response(null, 'Cart not found', 404);
            }

            if ($cart->status !== 'active') {
                return $this->response(null, 'Cannot update non-active cart', 403);
            }

            $validated = $request->validate([
                'type' => 'sometimes|in:dinein,takeaway',
                'table_number' => 'nullable|integer|min:1',
                'notes' => 'nullable|string',
            ]);

            if (isset($validated['type'])) {
                $cart->type = $validated['type'];
            }
            if (isset($validated['table_number'])) {
                $cart->table_number = $validated['table_number'];
            }
            if (isset($validated['notes'])) {
                $cart->notes = $validated['notes'];
            }

            $cart->save();

            return $this->response($cart->load('items'), 'Cart updated successfully');
        } catch (\Exception $e) {
            Log::error('API updateCart error: ' . $e->getMessage());
            return $this->response(null, 'Failed to update cart: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Add item to cart.
     * POST /api/cart/{id}/items
     */
    public function addCartItem(Request $request, $id)
    {
        try {
            $user = $this->getUser($request);
            if (!$user) {
                return $this->response(null, 'Unauthorized', 401);
            }

            $companyId = $user->company_id ?? 1;

            $cart = Cart::where('company_id', $companyId)
                ->where('user_id', $user->id)
                ->find($id);

            if (!$cart) {
                return $this->response(null, 'Cart not found', 404);
            }

            if ($cart->status !== 'active') {
                return $this->response(null, 'Cannot modify non-active cart', 403);
            }

            $validated = $request->validate([
                'menu_item_id' => 'nullable|exists:menu_items,id',
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'qty' => 'required|integer|min:1',
            ]);

            if ($validated['menu_item_id']) {
                $item = $cart->addItem(
                    (object) [
                        'id' => $validated['menu_item_id'],
                        'name' => $validated['name'],
                        'price' => $validated['price'],
                    ],
                    $validated['qty']
                );
            } else {
                $item = $cart->addAdditionalItem(
                    $validated['name'],
                    $validated['price'],
                    $validated['qty']
                );
            }

            $cart->recalculate();

            return $this->response([
                'cart' => $cart->load('items'),
                'item' => $item,
            ], 'Item added to cart', 201);
        } catch (\Exception $e) {
            Log::error('API addCartItem error: ' . $e->getMessage());
            return $this->response(null, 'Failed to add item: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update cart item quantity.
     * PUT /api/cart/{cartId}/items/{itemId}
     */
    public function updateCartItem(Request $request, $cartId, $itemId)
    {
        try {
            $user = $this->getUser($request);
            if (!$user) {
                return $this->response(null, 'Unauthorized', 401);
            }

            $companyId = $user->company_id ?? 1;

            $cart = Cart::where('company_id', $companyId)
                ->where('user_id', $user->id)
                ->find($cartId);

            if (!$cart) {
                return $this->response(null, 'Cart not found', 404);
            }

            if ($cart->status !== 'active') {
                return $this->response(null, 'Cannot modify non-active cart', 403);
            }

            $validated = $request->validate([
                'qty' => 'required|integer|min:0',
            ]);

            $item = $cart->updateItemQty($itemId, $validated['qty']);
            $cart->recalculate();

            return $this->response([
                'cart' => $cart->load('items'),
                'item' => $item,
            ], 'Cart item updated');
        } catch (\Exception $e) {
            Log::error('API updateCartItem error: ' . $e->getMessage());
            return $this->response(null, 'Failed to update cart item: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete cart item.
     * DELETE /api/cart/{cartId}/items/{itemId}
     */
    public function deleteCartItem($cartId, $itemId)
    {
        try {
            $user = $this->getUser(request());
            if (!$user) {
                return $this->response(null, 'Unauthorized', 401);
            }

            $companyId = $user->company_id ?? 1;

            $cart = Cart::where('company_id', $companyId)
                ->where('user_id', $user->id)
                ->find($cartId);

            if (!$cart) {
                return $this->response(null, 'Cart not found', 404);
            }

            if ($cart->status !== 'active') {
                return $this->response(null, 'Cannot modify non-active cart', 403);
            }

            $cart->removeItem($itemId);
            $cart->recalculate();

            return $this->response([
                'cart' => $cart->load('items'),
            ], 'Cart item deleted');
        } catch (\Exception $e) {
            Log::error('API deleteCartItem error: ' . $e->getMessage());
            return $this->response(null, 'Failed to delete cart item: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Apply discount to cart.
     * POST /api/cart/{id}/discount
     */
    public function applyCartDiscount(Request $request, $id)
    {
        try {
            $user = $this->getUser($request);
            if (!$user) {
                return $this->response(null, 'Unauthorized', 401);
            }

            $companyId = $user->company_id ?? 1;

            $cart = Cart::where('company_id', $companyId)
                ->where('user_id', $user->id)
                ->find($id);

            if (!$cart) {
                return $this->response(null, 'Cart not found', 404);
            }

            if ($cart->status !== 'active') {
                return $this->response(null, 'Cannot apply discount to non-active cart', 403);
            }

            $validated = $request->validate([
                'type' => 'required|in:rp,percent',
                'value' => 'required|numeric|min:0',
            ]);

            if ($validated['type'] === 'percent' && $validated['value'] > 100) {
                return $this->response(null, 'Percentage discount cannot exceed 100%', 422);
            }

            $cart->applyDiscount($validated['type'], $validated['value']);

            return $this->response([
                'cart' => $cart->load('items'),
            ], 'Discount applied successfully');
        } catch (\Exception $e) {
            Log::error('API applyCartDiscount error: ' . $e->getMessage());
            return $this->response(null, 'Failed to apply discount: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove discount from cart.
     * DELETE /api/cart/{id}/discount
     */
    public function removeCartDiscount($id)
    {
        try {
            $user = $this->getUser(request());
            if (!$user) {
                return $this->response(null, 'Unauthorized', 401);
            }

            $companyId = $user->company_id ?? 1;

            $cart = Cart::where('company_id', $companyId)
                ->where('user_id', $user->id)
                ->find($id);

            if (!$cart) {
                return $this->response(null, 'Cart not found', 404);
            }

            if ($cart->status !== 'active') {
                return $this->response(null, 'Cannot remove discount from non-active cart', 403);
            }

            $cart->removeDiscount();

            return $this->response([
                'cart' => $cart->load('items'),
            ], 'Discount removed');
        } catch (\Exception $e) {
            Log::error('API removeCartDiscount error: ' . $e->getMessage());
            return $this->response(null, 'Failed to remove discount: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Checkout (complete cart).
     * POST /api/cart/{id}/checkout
     */
    public function checkoutCart(Request $request, $id)
    {
        try {
            $user = $this->getUser($request);
            if (!$user) {
                return $this->response(null, 'Unauthorized', 401);
            }

            $companyId = $user->company_id ?? 1;

            $cart = Cart::where('company_id', $companyId)
                ->where('user_id', $user->id)
                ->find($id);

            if (!$cart) {
                return $this->response(null, 'Cart not found', 404);
            }

            if ($cart->items->isEmpty()) {
                return $this->response(null, 'Cart is empty', 422);
            }

            if ($cart->status !== 'active') {
                return $this->response(null, 'Cart already processed', 403);
            }

            // ============================================================
            // AMBIL SESSION_ID DARI CASHIER SESSION YANG SEDANG BUKA
            // ============================================================
            $cashierSession = CashierSession::open()->first();
            if (!$cashierSession) {
                throw new \Exception('No active cashier session found. Please open cashier first.');
            }
            $sessionId = $cashierSession->id;

            DB::beginTransaction();

            try {
                $cart->recalculate();

                // ---- Draft handling ----
                $draftId = null;
                if ($cart->notes) {
                    preg_match('/draft_id:(\d+)/', $cart->notes, $matches);
                    if (!empty($matches[1])) {
                        $draftId = (int) $matches[1];
                    }
                }

                if ($draftId) {
                    $draft = Draft::where('company_id', $companyId)->find($draftId);
                    if ($draft && $draft->status !== DraftStatusEnum::COMPLETED) {
                        $draft->status = DraftStatusEnum::COMPLETED;
                        $draft->save();
                        Log::info('Draft #' . $draft->id . ' completed during checkout of cart #' . $cart->id);
                    }
                }

                $cart->setCompleted();

                // ---- Transaction ----
                $transactionNumber = Transaction::generateTransactionNumber();
                $transaction = Transaction::create([
                    'cart_id'            => $cart->id,
                    'draft_id'           => $draftId,
                    'user_id'            => $user->id,
                    'company_id'         => $companyId,
                    'session_id'         => $sessionId, // <-- session_id dari sesi kasir aktif
                    'transaction_number' => $transactionNumber,
                    'transaction_date'   => now(),
                    'subtotal'           => $cart->subtotal,
                    'discount_type'      => $cart->discount_type,
                    'discount_value'     => $cart->discount_value ?? 0,
                    'discount_amount'    => $cart->discount_amount ?? 0,
                    'total'              => $cart->total,
                    'payment_method'     => $request->input('payment_method', 'cash'),
                    'paid'               => $request->input('paid', $cart->total),
                    'change'             => $request->input('change', 0),
                    'status'             => 'completed',
                ]);

                foreach ($cart->items as $cartItem) {
                    TransactionItem::create([
                        'transaction_id'    => $transaction->id,
                        'menu_item_id'      => $cartItem->menu_item_id,
                        'name'              => $cartItem->name,
                        'price'             => $cartItem->price,
                        'qty'               => $cartItem->qty,
                        'subtotal'          => $cartItem->price * $cartItem->qty,
                        'discount_per_item' => 0,
                    ]);
                }

                // ============================================================
                // STOCK, MOVEMENT, INVENTORY REPORT (with ReportPeriod)
                // ============================================================

                // 1. Group quantities by variant
                $variantUpdates = [];
                foreach ($cart->items as $cartItem) {
                    $menuItem = MenuItem::find($cartItem->menu_item_id);
                    if ($menuItem && $menuItem->product_variant_id) {
                        $variantId = $menuItem->product_variant_id;
                        $variantUpdates[$variantId] = ($variantUpdates[$variantId] ?? 0) + $cartItem->qty;
                    }
                }

                // 2. Get shift_id based on current time and company
                $currentTime = now()->format('H:i:s');
                $shift = Shift::where('company_id', $companyId)
                    ->where(function ($query) use ($currentTime) {
                        // Shift yang tidak melewati tengah malam (start < end)
                        $query->where(function ($q) use ($currentTime) {
                            $q->where('start_time', '<=', $currentTime)
                                ->where('end_time', '>', $currentTime);
                        })
                            // Shift yang melewati tengah malam (start > end, misal 22:00 - 06:00)
                            ->orWhere(function ($q) use ($currentTime) {
                                $q->where('start_time', '>', 'end_time')
                                    ->where(function ($q2) use ($currentTime) {
                                        $q2->where('start_time', '<=', $currentTime)
                                            ->orWhere('end_time', '>', $currentTime);
                                    });
                            });
                    })
                    ->first();

                // Jika tidak ketemu, ambil shift pertama (fallback)
                if (!$shift) {
                    $shift = Shift::where('company_id', $companyId)->first();
                    if (!$shift) {
                        throw new \Exception('No shift found for company ID ' . $companyId);
                    }
                }
                $shiftId = $shift->id;

                // 3. Get or create the report period for today
                $today = now()->toDateString();

                $reportPeriod = ReportPeriod::where('company_id', $companyId)
                    ->where('date', $today)
                    ->where('is_active', true)
                    ->first();

                if (!$reportPeriod) {
                    $reportPeriod = ReportPeriod::create([
                        'company_id' => $companyId,
                        'date'       => $today,
                        'name'       => 'Daily Report ' . $today,
                        'is_active'  => true,
                        'shift_id'   => $shiftId,
                    ]);
                    Log::info('New report period created for date: ' . $today . ' with shift_id: ' . $shiftId);
                }

                // 4. Process each variant
                $inventoryReport = null;

                foreach ($variantUpdates as $variantId => $totalQty) {
                    $stock = Stock::where('product_variant_id', $variantId)->first();
                    if (!$stock) {
                        Log::warning("Stock not found for variant ID $variantId during checkout");
                        continue;
                    }

                    $stockBefore = (int) $stock->current_stock;
                    $stockAfter = $stockBefore - $totalQty;

                    if ($stockAfter < 0) {
                        throw new \Exception("Insufficient stock for variant ID $variantId. Available: $stockBefore, requested: $totalQty");
                    }

                    $stock->current_stock = $stockAfter;
                    $stock->save();

                    StockMovement::create([
                        'product_variant_id' => $variantId,
                        'pic_id'             => $user->id,
                        'movement_type'      => 'selling',
                        'qty'                => $totalQty,
                        'stock_before'       => $stockBefore,
                        'stock_after'        => $stockAfter,
                        'notes'              => "Penjualan dari transaksi #{$transaction->transaction_number}",
                        'receiver_sender'    => $user->name,
                    ]);

                    // ---- Inventory Report ----
                    if (!$inventoryReport) {
                        $inventoryReport = InventoryReport::firstOrCreate(
                            [
                                'report_period_id' => $reportPeriod->id,
                                'location'         => $companyId,
                            ],
                            [
                                'report_date'         => $today,
                                'reported_by'         => $user->id,
                                'opened_at'           => now(),
                                'cashier_name'        => $user->name,
                                'total_products_sold' => 0,
                                'notes'               => 'Auto generated report',
                            ]
                        );
                    }

                    $reportItem = InventoryReportItem::where('inventory_report_id', $inventoryReport->id)
                        ->where('product_variant_id', $variantId)
                        ->first();

                    if ($reportItem) {
                        $reportItem->selling += $totalQty;
                        $reportItem->remain = $reportItem->first_stock + $reportItem->stock_in - $reportItem->selling;
                        $reportItem->save();
                    } else {
                        InventoryReportItem::create([
                            'inventory_report_id' => $inventoryReport->id,
                            'product_variant_id'  => $variantId,
                            'first_stock'         => $stockBefore,
                            'stock_in'            => 0,
                            'selling'             => $totalQty,
                            'remain'              => $stockBefore - $totalQty,
                        ]);
                    }

                    $inventoryReport->total_products_sold += $totalQty;
                    $inventoryReport->save();
                }

                DB::commit();

                // ---- Dispatch print job ----
                try {
                    PrintReceiptJob::dispatch($transaction);
                } catch (\Exception $e) {
                    Log::warning('Failed to dispatch print job: ' . $e->getMessage());
                }

                // ---- Response ----
                $responseData = [
                    'id'                 => $transaction->id,
                    'transaction_number' => $transaction->transaction_number,
                    'transaction_date'   => $transaction->transaction_date->toISOString(),
                    'subtotal'           => $transaction->subtotal,
                    'discount_type'      => $transaction->discount_type,
                    'discount_value'     => $transaction->discount_value,
                    'discount_amount'    => $transaction->discount_amount,
                    'total'              => $transaction->total,
                    'payment_method'     => $transaction->payment_method,
                    'paid'               => $transaction->paid,
                    'change'             => $transaction->change,
                    'items'              => $transaction->items->map(function ($item) {
                        return [
                            'name'     => $item->name,
                            'price'    => $item->price,
                            'qty'      => $item->qty,
                            'subtotal' => $item->subtotal,
                        ];
                    }),
                ];

                return $this->response([
                    'cart'        => $cart->load('items'),
                    'transaction' => $responseData,
                ], 'Checkout successful');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Checkout transaction error: ' . $e->getMessage());
                return $this->response(null, 'Checkout failed: ' . $e->getMessage(), 500);
            }
        } catch (\Exception $e) {
            Log::error('API checkoutCart error: ' . $e->getMessage());
            Log::error('API checkoutCart trace: ' . $e->getTraceAsString());
            return $this->response(null, 'Checkout failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Move draft to cart (using user_id, not session).
     * POST /api/drafts/{id}/to-cart
     */
    public function moveDraftToCart(Request $request, $id)
    {
        try {
            $user = $this->getUser($request);
            if (!$user) {
                return $this->response(null, 'Unauthorized', 401);
            }

            $companyId = $user->company_id ?? 1;

            $draft = Draft::with('items')
                ->where('company_id', $companyId)
                ->find($id);

            if (!$draft) {
                return $this->response(null, 'Draft not found', 404);
            }

            if ($draft->status === DraftStatusEnum::COMPLETED) {
                return $this->response(null, 'Draft already completed', 403);
            }

            if ($draft->items->isEmpty()) {
                return $this->response(null, 'Draft is empty', 422);
            }

            // Cari atau buat cart aktif untuk user ini
            $cart = Cart::where('company_id', $companyId)
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if (!$cart) {
                $cart = Cart::create([
                    'company_id' => $companyId,
                    'user_id'    => $user->id,
                    'type'       => $draft->type,
                    'table_number' => $draft->table_number,
                    'status'     => 'active',
                    'subtotal'   => 0,
                    'total'      => 0,
                    'notes'      => 'draft_id:' . $draft->id,
                ]);
            } else {
                // Jika cart sudah ada, tambahkan draft_id ke notes (append)
                $notes = $cart->notes ?: '';
                if (strpos($notes, 'draft_id:') === false) {
                    $cart->notes = trim($notes . ' draft_id:' . $draft->id);
                    $cart->save();
                }
            }

            // Pindahkan item draft ke cart
            foreach ($draft->items as $draftItem) {
                $cart->addItem(
                    (object) [
                        'id'    => $draftItem->menu_item_id,
                        'name'  => $draftItem->name,
                        'price' => $draftItem->price,
                    ],
                    $draftItem->qty
                );
            }

            $cart->recalculate();

            // ============================================================
            // Kosongkan draft (hapus semua item) dan ubah status menjadi COMPLETED
            // ============================================================
            $draft->items()->delete(); // hapus semua item draft
            $draft->status = DraftStatusEnum::COMPLETED;
            $draft->save();

            Log::info('Draft #' . $draft->id . ' moved to cart and cleared');

            return $this->response([
                'cart'      => $cart->load('items'),
                'draftId'   => $draft->id,
                'draftName' => $draft->name,
            ], 'Draft moved to cart successfully');
        } catch (\Exception $e) {
            Log::error('API moveDraftToCart error: ' . $e->getMessage());
            return $this->response(null, 'Failed to move draft to cart: ' . $e->getMessage(), 500);
        }
    }

    // ============================================================
    // DRAFT ITEMS MANAGEMENT
    // ============================================================

    /**
     * Add item to draft, or update qty if exists.
     * POST /api/drafts/{id}/items
     */
    public function addDraftItem(Request $request, $id)
    {
        try {
            $user = $this->getUser($request);
            if (!$user) {
                return $this->response(null, 'Unauthorized', 401);
            }

            $companyId = $user->company_id ?? 1;

            $draft = Draft::where('company_id', $companyId)->find($id);
            if (!$draft) {
                return $this->response(null, 'Draft not found', 404);
            }
            if ($draft->status !== DraftStatusEnum::ACTIVE) {
                return $this->response(null, 'Draft is not active. Please activate it first.', 403);
            }

            $validated = $request->validate([
                'menu_item_id' => 'nullable|exists:menu_items,id',
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'qty' => 'required|integer|min:1',
            ]);

            $existingItem = null;
            if ($validated['menu_item_id']) {
                $existingItem = DraftItem::where('draft_id', $draft->id)
                    ->where('menu_item_id', $validated['menu_item_id'])
                    ->first();
            }

            if ($existingItem) {
                $newQty = $existingItem->qty + $validated['qty'];
                $existingItem->qty = $newQty;
                $existingItem->total = $existingItem->price * $newQty;
                $existingItem->save();
                $item = $existingItem;
            } else {
                $total = $validated['price'] * $validated['qty'];
                $item = DraftItem::create([
                    'draft_id' => $draft->id,
                    'menu_item_id' => $validated['menu_item_id'] ?? null,
                    'name' => $validated['name'],
                    'price' => $validated['price'],
                    'qty' => $validated['qty'],
                    'total' => $total,
                ]);
            }

            $draft->refresh();
            $draft->load('items');
            $draft->recalculate();
            $draft->refresh();

            return $this->response([
                'draft' => $draft,
                'item' => $item,
            ], 'Item added to draft', 201);
        } catch (\Exception $e) {
            Log::error('API addDraftItem error: ' . $e->getMessage());
            return $this->response(null, 'Failed to add item: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update item quantity in draft.
     * PUT /api/drafts/{draftId}/items/{itemId}
     */
    public function updateDraftItem(Request $request, $draftId, $itemId)
    {
        try {
            $user = $this->getUser($request);
            if (!$user) {
                return $this->response(null, 'Unauthorized', 401);
            }

            $companyId = $user->company_id ?? 1;

            $draft = Draft::where('company_id', $companyId)->find($draftId);
            if (!$draft) {
                return $this->response(null, 'Draft not found', 404);
            }
            if ($draft->status !== DraftStatusEnum::ACTIVE) {
                return $this->response(null, 'Cannot update item in non-active draft', 403);
            }

            $item = DraftItem::where('draft_id', $draft->id)->find($itemId);
            if (!$item) {
                return $this->response(null, 'Item not found', 404);
            }

            $validated = $request->validate([
                'qty' => 'required|integer|min:0',
            ]);

            if ($validated['qty'] == 0) {
                $item->delete();
            } else {
                $item->qty = $validated['qty'];
                $item->total = $item->price * $item->qty;
                $item->save();
            }

            $draft->refresh();
            $draft->load('items');
            $draft->recalculate();
            $draft->refresh();

            return $this->response([
                'draft' => $draft,
                'item' => $validated['qty'] == 0 ? null : $item,
            ], 'Item updated');
        } catch (\Exception $e) {
            Log::error('API updateDraftItem error: ' . $e->getMessage());
            return $this->response(null, 'Failed to update item: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete item from draft.
     * DELETE /api/drafts/{draftId}/items/{itemId}
     */
    public function deleteDraftItem(Request $request, $draftId, $itemId)
    {
        try {
            $user = $this->getUser($request);
            if (!$user) {
                return $this->response(null, 'Unauthorized', 401);
            }

            $companyId = $user->company_id ?? 1;

            $draft = Draft::where('company_id', $companyId)->find($draftId);
            if (!$draft) {
                return $this->response(null, 'Draft not found', 404);
            }
            if ($draft->status !== DraftStatusEnum::ACTIVE) {
                return $this->response(null, 'Cannot delete item from non-active draft', 403);
            }

            $item = DraftItem::where('draft_id', $draft->id)->find($itemId);
            if (!$item) {
                return $this->response(null, 'Item not found', 404);
            }

            $item->delete();

            $draft->refresh();
            $draft->load('items');
            $draft->recalculate();
            $draft->refresh();

            return $this->response([
                'draft' => $draft,
            ], 'Item deleted');
        } catch (\Exception $e) {
            Log::error('API deleteDraftItem error: ' . $e->getMessage());
            return $this->response(null, 'Failed to delete item: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Menghasilkan nomor transaksi baru dengan counter harian.
     * Format: KITA/YYYY/MM/DD/XXXX (XXXX = 4 digit, reset tiap hari)
     */
    public function generateTrxNumber()
    {
        $date = now()->format('Ymd');
        $key  = "trx_counter_{$date}";

        $counter = Cache::get($key, 0);
        $counter++;

        Cache::put($key, $counter, now()->endOfDay());

        $year   = now()->year;
        $month  = now()->month;
        $day    = now()->day;
        $padded = str_pad($counter, 4, '0', STR_PAD_LEFT);

        $data = "KITA/{$year}/{$month}/{$day}/{$padded}";

        return $this->response($data);
    }

    /**
     * Check stock status of a menu item.
     * GET /api/menu/{id}/stock
     */
    public function getStockStatus($id)
    {
        try {
            $menuItem = MenuItem::with('productVariant.stock')->find($id);
            if (!$menuItem) {
                return $this->response(null, 'Menu item not found', 404);
            }

            $variantId = $menuItem->product_variant_id;
            $perMenuStock = 0;

            if ($variantId) {
                $count = MenuItem::where('product_variant_id', $variantId)->count();
                $totalStock = (int) ($menuItem->productVariant->stock->current_stock ?? 0);
                $perMenuStock = $count > 0 ? floor($totalStock / $count) : 0;
            } else {
                $perMenuStock = (int) ($menuItem->stock ?? 0);
            }

            $status = $this->determineStatus($perMenuStock);

            return $this->response([
                'menu_item_id' => $menuItem->id,
                'product_variant_id' => $variantId,
                'stock' => $perMenuStock,
                'total_variant_stock' => $variantId ? (int) ($menuItem->productVariant->stock->current_stock ?? 0) : null,
                'status' => $status,
                'status_label' => $this->getStatusLabel($status),
            ]);
        } catch (\Exception $e) {
            Log::error('API getStockStatus error: ' . $e->getMessage());
            return $this->response(null, 'Failed to get stock status', 500);
        }
    }

    /**
     * Update menu item status based on current stock.
     * POST /api/menu/{id}/update-status
     */
    public function updateStockStatus($id)
    {
        try {
            $menuItem = MenuItem::with('productVariant.stock')->find($id);
            if (!$menuItem) {
                return $this->response(null, 'Menu item not found', 404);
            }

            $variantId = $menuItem->product_variant_id;
            $perMenuStock = 0;

            if ($variantId) {
                $count = MenuItem::where('product_variant_id', $variantId)->count();
                $totalStock = (int) ($menuItem->productVariant->stock->current_stock ?? 0);
                $perMenuStock = $count > 0 ? floor($totalStock / $count) : 0;
            } else {
                $perMenuStock = (int) ($menuItem->stock ?? 0);
            }

            $newStatus = $this->determineStatus($perMenuStock);
            $menuItem->status = $newStatus;
            $menuItem->save();

            return $this->response([
                'menu_item_id' => $menuItem->id,
                'product_variant_id' => $variantId,
                'stock' => $perMenuStock,
                'old_status' => $menuItem->getOriginal('status'),
                'new_status' => $newStatus,
            ], 'Stock status updated');
        } catch (\Exception $e) {
            Log::error('API updateStockStatus error: ' . $e->getMessage());
            return $this->response(null, 'Failed to update stock status', 500);
        }
    }

    /**
     * Determine status based on stock quantity.
     */
    private function determineStatus($stock)
    {
        if ($stock <= 0) {
            return MenuStatusEnum::OUT->value;
        } elseif ($stock <= 25) {
            return MenuStatusEnum::LOW->value;
        } else {
            return MenuStatusEnum::AVAILABLE->value;
        }
    }

    /**
     * Get label for status.
     */
    private function getStatusLabel($status)
    {
        foreach (MenuStatusEnum::cases() as $case) {
            if ($case->value === $status) {
                return $case->label();
            }
        }
        return ucfirst($status);
    }


    /**
     * Print Transaction Receipt via PrintService.
     * GET /api/transactions/{id}/print 
     */
    public function printTransaction($id, PrintService $printService)
    {
        try {
            $user = $this->getUser(request());
            if (!$user) {
                return $this->response(null, 'Unauthorized', 401);
            }

            $companyId = $user->company_id ?? 1;
            $transaction = Transaction::with(['items', 'user', 'company'])
                ->where('company_id', $companyId)
                ->find($id);

            if (!$transaction) {
                return $this->response(null, 'Transaction not found', 404);
            }

            // Coba print via PrintService
            $printService->printReceipt($transaction);

            return $this->response(null, 'Struk berhasil dicetak via server');
        } catch (\Exception $e) {
            // Jika gagal, kita kirim response dengan status 500 agar JS bisa fallback
            Log::error('Print transaction error: ' . $e->getMessage());
            return $this->response(null, 'Gagal mencetak via server: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get summary of current shift transactions.
     * GET /api/cashier/shift-summary
     */
    public function getShiftSummary(Request $request, CashierService $cashierService)
    {
        try {
            $user = $this->getUser($request);
            if (!$user) {
                return $this->response(null, 'Unauthorized', 401);
            }

            $summary = $cashierService->getShiftTransactionsSummary();

            return $this->response($summary, 'Shift summary retrieved successfully');
        } catch (\Exception $e) {
            Log::error('API getShiftSummary error: ' . $e->getMessage());
            return $this->response(null, 'Failed to get shift summary: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Open a new cashier session.
     * POST /api/cashier/open
     */
    public function openCashier(Request $request, CashierService $cashierService)
    {
        try {
            $user = $this->getUser($request);
            if (!$user) {
                return $this->response(null, 'Unauthorized', 401);
            }

            $session = $cashierService->openCashier();

            return $this->response([
                'session_id'      => $session->id,
                'opening_balance' => (float) $session->opening_balance,
                'opened_at'       => $session->opened_at->toISOString(),
                'user_id'         => $session->user_id,
            ], 'Cashier session opened successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->response(null, $e->getMessage(), 422);
        } catch (\Exception $e) {
            Log::error('API openCashier error: ' . $e->getMessage());
            Log::error('API openCashier trace: ' . $e->getTraceAsString());
            return $this->response(null, 'Failed to open cashier: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Close the current cashier session.
     * POST /api/cashier/close
     */
    public function closeCashier(Request $request, CashierService $cashierService)
    {
        try {
            $user = $this->getUser($request);
            if (!$user) {
                return $this->response(null, 'Unauthorized', 401);
            }

            // Validasi input
            $validated = $request->validate([
                'actual_balance' => 'required|numeric|min:0',
            ]);

            $session = $cashierService->closeCashier((float) $validated['actual_balance']);

            return $this->response([
                'session_id'       => $session->id,
                'opening_balance'  => (float) $session->opening_balance,
                'closing_balance'  => (float) $session->closing_balance,
                'total_sales'      => (float) $session->total_sales,
                'closed_at'        => $session->closed_at->toISOString(),
                'user_id'          => $session->user_id,
            ], 'Cashier session closed successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->response(null, $e->getMessage(), 422);
        } catch (\Exception $e) {
            Log::error('API closeCashier error: ' . $e->getMessage());
            Log::error('API closeCashier trace: ' . $e->getTraceAsString());
            return $this->response(null, 'Failed to close cashier: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get current cashier session status.
     * GET /api/cashier/status
     */
    public function getCashierStatus(Request $request)
    {
        try {
            $user = $this->getUser($request);
            if (!$user) {
                return $this->response(null, 'Unauthorized', 401);
            }

            $activeSession = CashierSession::open()->first();

            if ($activeSession) {
                return $this->response([
                    'is_open'          => true,
                    'session_id'       => $activeSession->id,
                    'opening_balance'  => (float) $activeSession->opening_balance,
                    'opened_at'        => $activeSession->opened_at->toISOString(),
                    'user_id'          => $activeSession->user_id,
                    'user_name'        => $activeSession->user->name ?? 'Unknown',
                ], 'Cashier is open');
            }

            return $this->response([
                'is_open' => false,
            ], 'Cashier is closed');
        } catch (\Exception $e) {
            Log::error('API getCashierStatus error: ' . $e->getMessage());
            return $this->response(null, 'Failed to get cashier status: ' . $e->getMessage(), 500);
        }
    }
}
