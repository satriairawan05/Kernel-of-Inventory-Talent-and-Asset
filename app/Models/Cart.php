<?php
// app/Models/Cart.php

namespace App\Models;

use App\Enums\CartDiscountTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'session_id',
        'type',
        'table_number',
        'status',
        'subtotal',
        'discount_amount',
        'discount_type',
        'discount_value',
        'total',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'total' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    // ===== RELATIONSHIPS =====

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // ===== HELPERS - ITEMS =====

    /**
     * Add a menu item to cart.
     */
    public function addItem($menuItem, int $qty = 1): CartItem
    {
        // Cek apakah item sudah ada di cart
        $existing = $this->items()
            ->where('menu_item_id', $menuItem->id)
            ->first();

        if ($existing) {
            $existing->qty += $qty;
            $existing->save();
            return $existing;
        }

        return $this->items()->create([
            'menu_item_id' => $menuItem->id,
            'name'         => $menuItem->name,
            'price'        => $menuItem->price,
            'qty'          => $qty,
        ]);
    }

    /**
     * Add an additional item (without menu_item_id).
     */
    public function addAdditionalItem(string $name, float $price, int $qty = 1): CartItem
    {
        return $this->items()->create([
            'menu_item_id' => null,
            'name'         => $name,
            'price'        => $price,
            'qty'          => $qty,
        ]);
    }

    /**
     * Remove an item from cart.
     */
    public function removeItem(int $itemId): bool
    {
        return $this->items()->where('id', $itemId)->delete() > 0;
    }

    /**
     * Update item quantity.
     * Returns the updated item or null if removed.
     */
    public function updateItemQty(int $itemId, int $qty): ?CartItem
    {
        $item = $this->items()->find($itemId);
        if (!$item) {
            return null;
        }

        if ($qty <= 0) {
            $item->delete();
            return null;
        }

        $item->qty = $qty;
        $item->save();
        return $item;
    }

    // ===== HELPERS - DISCOUNT =====

    /**
     * Apply discount to cart.
     */
    public function applyDiscount(string $type, float $value): self
    {
        // Validasi tipe diskon
        if (!CartDiscountTypeEnum::isValid($type)) {
            throw new \InvalidArgumentException('Invalid discount type');
        }

        if ($type === CartDiscountTypeEnum::PERCENT && $value > 100) {
            throw new \InvalidArgumentException('Percentage discount cannot exceed 100');
        }

        $this->discount_type = $type;
        $this->discount_value = $value;
        $this->recalculate();
        return $this;
    }

    /**
     * Remove discount from cart.
     */
    public function removeDiscount(): self
    {
        $this->discount_type = null;
        $this->discount_value = 0;
        $this->discount_amount = 0;
        $this->recalculate();
        return $this;
    }

    /**
     * Check if cart has discount applied.
     */
    public function hasDiscount(): bool
    {
        return !empty($this->discount_type) && $this->discount_type !== CartDiscountTypeEnum::NONE;
    }

    // ===== HELPERS - CALCULATION =====

    /**
     * Recalculate subtotal, discount, and total.
     */
    public function recalculate(): self
    {
        $this->load('items');

        // Subtotal = sum of all item subtotals
        $this->subtotal = $this->items->sum('subtotal');

        // Calculate discount
        if ($this->discount_type === CartDiscountTypeEnum::RP) {
            $this->discount_amount = min($this->discount_value, $this->subtotal);
        } elseif ($this->discount_type === CartDiscountTypeEnum::PERCENT) {
            $pct = min($this->discount_value, 100);
            $this->discount_amount = $this->subtotal * $pct / 100;
        } else {
            $this->discount_amount = 0;
        }

        // Total = subtotal - discount
        $this->total = max($this->subtotal - $this->discount_amount, 0);

        $this->save();
        return $this;
    }

    // ===== HELPERS - STATUS =====

    public function getTotalQtyAttribute(): int
    {
        return $this->items->sum('qty');
    }

    public function getTotalItemsAttribute(): int
    {
        return $this->items->count();
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'dinein' ? '🍽️ Dine In' : '🛍️ Take Away';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Set status to active.
     */
    public function setActive(): self
    {
        $this->status = 'active';
        $this->save();
        return $this;
    }

    /**
     * Set status to processing.
     */
    public function setProcessing(): self
    {
        $this->status = 'processing';
        $this->save();
        return $this;
    }

    /**
     * Set status to completed.
     */
    public function setCompleted(): self
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();
        return $this;
    }

    // ===== SCOPES =====

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeForUser($query, ?int $userId)
    {
        if ($userId) {
            return $query->where('user_id', $userId);
        }
        return $query;
    }

    public function scopeForSession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }
}