<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\{Company, MenuItem, Shift, Draft, DraftItem};
use App\Enums\{DraftTypeEnum, DraftStatusEnum};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
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

    /**
     * Move draft items to cart (frontend will merge to cart).
     * POST /api/drafts/{id}/to-cart
     */
    public function moveDraftToCart($id)
    {
        try {
            $user = auth()->user();
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

            $items = $draft->items->map(function ($item) {
                return [
                    'id'    => $item->menu_item_id ?? $item->id,
                    'name'  => $item->name,
                    'price' => (float) $item->price,
                    'qty'   => $item->qty,
                    'total' => (float) $item->total,
                ];
            });

            $draft->markAsProcessing();

            return $this->response([
                'items'   => $items,
                'draftId' => $draft->id,
                'name'    => $draft->name,
            ], 'Draft moved to cart successfully');
        } catch (\Exception $e) {
            Log::error('API moveDraftToCart error: ' . $e->getMessage());
            return $this->response(null, 'Failed to move draft to cart', 500);
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

            // Cek apakah item dengan menu_item_id sudah ada di draft ini
            $existingItem = null;
            if ($validated['menu_item_id']) {
                $existingItem = DraftItem::where('draft_id', $draft->id)
                    ->where('menu_item_id', $validated['menu_item_id'])
                    ->first();
            }

            if ($existingItem) {
                // Update qty existing
                $newQty = $existingItem->qty + $validated['qty'];
                $existingItem->qty = $newQty;
                $existingItem->total = $existingItem->price * $newQty;
                $existingItem->save();
                $item = $existingItem;
            } else {
                // Buat item baru
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

            // Recalculate subtotal
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

            // Recalculate dan refresh
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

            // Recalculate dan refresh
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
