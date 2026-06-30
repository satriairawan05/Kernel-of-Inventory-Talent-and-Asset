<?php

namespace App\Http\Controllers\API;

use App\Enums\{DraftTypeEnum, DraftStatusEnum};
use App\Http\Controllers\Controller;
use App\Models\{Company, MenuItem, Shift, Draft, DraftItem, Cart};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log, Storage};
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
            $user = auth()->user();
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
            $user = auth()->user();
            $companyId = $request->has('company_id') ? $request->company_id : ($user->company_id ?? 1);

            $query = MenuItem::with(['productVariant.stock'])
                ->when($companyId, function ($q) use ($companyId) {
                    return $q->where('company_id', $companyId);
                })
                ->where('status', '!=', 'out')
                ->orderBy('name');

            if ($request->has('category') && $request->category != 'all') {
                $query->where('category', $request->category);
            }

            $menu = $query->get();

            $formatted = $menu->map(function ($item) {
                $imageUrl = null;
                if (method_exists($item, 'getImageUrlAttribute')) {
                    $imageUrl = $item->image_url;
                } elseif ($item->image) {
                    $imageUrl = asset('storage/' . $item->image);
                }

                return [
                    'id'       => $item->id,
                    'name'     => $item->name,
                    'price'    => (int) $item->price,
                    'category' => $item->category,
                    'status'   => $item->status,
                    'image'    => $imageUrl,
                    'stock'    => (int) ($item->stock ?? 0),
                    'icon'     => $this->getInitials($item->name),
                    'variant'  => $item->productVariant ? [
                        'id'    => $item->productVariant->id,
                        'name'  => $item->productVariant->variant_name,
                        'stock' => (int) ($item->productVariant->stock?->current_stock ?? 0),
                    ] : null,
                ];
            });

            return $this->response($formatted);
        } catch (\Exception $e) {
            Log::error('API getMenu error: ' . $e->getMessage());
            Log::error('API getMenu trace: ' . $e->getTraceAsString());
            return $this->response(null, 'Failed to fetch menu: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Create a new menu item (including additional).
     * POST /api/menu
     * Allowed for all authenticated users (no admin restriction for additional)
     */
    public function storeMenu(Request $request)
    {
        try {
            $user = auth()->user();
            $companyId = $request->input('company_id', $user->company_id ?? 1);

            $validated = $request->validate([
                'name'     => 'required|string|max:255',
                'price'    => 'required|numeric|min:0',
                'category' => 'required|in:food,drink,snack,additional',
                'status'   => 'required|in:available,low,out',
                'image'    => 'nullable|image|max:2048',
            ]);

            // Jika kategori "additional", set product_variant_id dan stock = null
            // Jika bukan additional, tetap bisa disimpan dengan product_variant_id null
            // (tapi untuk regular menu, biasanya harus ada product_variant_id, kita tetap nullable)

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('menu_images', 'public');
            }

            $menu = MenuItem::create([
                'company_id'         => $companyId,
                'product_variant_id' => null, // additional tidak terikat varian
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
     * Update a menu item (including additional).
     * PUT /api/menu/{id}
     * Only admin (group_id = 1) can update.
     */
    public function updateMenu(Request $request, $id)
    {
        try {
            $user = auth()->user();

            // === ONLY ADMIN (group_id = 1) ===
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
     * Only admin (group_id = 1) can delete.
     */
    public function deleteMenu($id)
    {
        try {
            $user = auth()->user();

            // === ONLY ADMIN (group_id = 1) ===
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
            $user = auth()->user();
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
            $user = auth()->user();
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
            $user = auth()->user();
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
            $user = auth()->user();
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
     * Get active cart for current user/session.
     * GET /api/cart
     */
    public function getCart(Request $request)
    {
        try {
            $user = auth()->user();
            $companyId = $user->company_id ?? 1;
            $sessionId = $request->session()->getId();

            $cart = Cart::forCompany($companyId)
                ->forUser($user?->id)
                ->forSession($sessionId)
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
     * Create a new cart (or get existing).
     * POST /api/cart
     */
    public function createCart(Request $request)
    {
        try {
            $user = auth()->user();
            $companyId = $user->company_id ?? 1;
            $sessionId = $request->session()->getId();

            $validated = $request->validate([
                'type' => 'required|in:dinein,takeaway',
                'table_number' => 'nullable|integer|min:1',
            ]);

            $existing = Cart::forCompany($companyId)
                ->forUser($user?->id)
                ->forSession($sessionId)
                ->where('status', 'active')
                ->first();

            if ($existing) {
                return $this->response($existing->load('items'), 'Existing cart found');
            }

            $cart = Cart::create([
                'company_id' => $companyId,
                'user_id' => $user?->id,
                'session_id' => $sessionId,
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
            $user = auth()->user();
            $companyId = $user->company_id ?? 1;

            $cart = Cart::forCompany($companyId)->find($id);
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
            $user = auth()->user();
            $companyId = $user->company_id ?? 1;

            $cart = Cart::forCompany($companyId)->find($id);
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
            $user = auth()->user();
            $companyId = $user->company_id ?? 1;

            $cart = Cart::forCompany($companyId)->find($cartId);
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
            $user = auth()->user();
            $companyId = $user->company_id ?? 1;

            $cart = Cart::forCompany($companyId)->find($cartId);
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
            $user = auth()->user();
            $companyId = $user->company_id ?? 1;

            $cart = Cart::forCompany($companyId)->find($id);
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
            $user = auth()->user();
            $companyId = $user->company_id ?? 1;

            $cart = Cart::forCompany($companyId)->find($id);
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
            $user = auth()->user();
            $companyId = $user->company_id ?? 1;

            $cart = Cart::forCompany($companyId)->find($id);
            if (!$cart) {
                return $this->response(null, 'Cart not found', 404);
            }

            if ($cart->items->isEmpty()) {
                return $this->response(null, 'Cart is empty', 422);
            }

            if ($cart->status !== 'active') {
                return $this->response(null, 'Cart already processed', 403);
            }

            $cart->recalculate();
            $cart->markAsCompleted();

            // TODO: Create transaction / invoice from cart data

            return $this->response([
                'cart' => $cart->load('items'),
                'transaction' => [
                    'id' => $cart->id,
                    'total' => $cart->total,
                    'items' => $cart->items,
                ],
            ], 'Checkout successful');
        } catch (\Exception $e) {
            Log::error('API checkoutCart error: ' . $e->getMessage());
            return $this->response(null, 'Checkout failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Move draft to cart.
     * POST /api/drafts/{id}/to-cart
     */
    public function moveDraftToCart(Request $request, $id)
    {
        try {
            $user = auth()->user();
            $companyId = $user->company_id ?? 1;
            $sessionId = $request->session()->getId();

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

            $cart = Cart::forCompany($companyId)
                ->forUser($user?->id)
                ->forSession($sessionId)
                ->where('status', 'active')
                ->first();

            if (!$cart) {
                $cart = Cart::create([
                    'company_id' => $companyId,
                    'user_id' => $user?->id,
                    'session_id' => $sessionId,
                    'type' => $draft->type,
                    'table_number' => $draft->table_number,
                    'status' => 'active',
                    'subtotal' => 0,
                    'total' => 0,
                ]);
            }

            foreach ($draft->items as $draftItem) {
                $cart->addItem(
                    (object) [
                        'id' => $draftItem->menu_item_id,
                        'name' => $draftItem->name,
                        'price' => $draftItem->price,
                    ],
                    $draftItem->qty
                );
            }

            $cart->recalculate();
            $draft->markAsProcessing();

            return $this->response([
                'cart' => $cart->load('items'),
                'draftId' => $draft->id,
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
            $user = auth()->user();
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
            $user = auth()->user();
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
            $user = auth()->user();
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
}