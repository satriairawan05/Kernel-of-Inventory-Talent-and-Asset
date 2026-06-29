<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class MenuItem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'menu_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'product_variant_id',
        'name',
        'price',
        'category',
        'status',
        'image',
        'stock',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'integer',
        'stock' => 'integer',
    ];

    // ========== RELATIONS ==========

    /**
     * Get the company that owns the menu item.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the product variant that this menu item is linked to.
     */
    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function draftItems(): HasMany
    {
        return $this->hasMany(DraftItem::class);
    }

    /**
     * Get the stock record through the product variant.
     * This is a convenience accessor.
     */
    public function stock()
    {
        return $this->hasOneThrough(
            Stock::class,
            ProductVariant::class,
            'id',          // Foreign key on ProductVariant
            'product_variant_id', // Foreign key on Stock
            'product_variant_id', // Local key on MenuItem
            'id'           // Local key on ProductVariant
        );
    }

    // ========== SCOPES ==========

    /**
     * Scope a query to only include menu items for a specific company.
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope a query to only include available menu items.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    // ========== ACCESSORS ==========

    /**
     * Get the current stock from the linked product variant.
     * Falls back to 0 if no variant is linked.
     */
    public function getCurrentStockAttribute(): int
    {
        return $this->productVariant?->stock?->current_stock ?? 0;
    }

    /**
     * Get the full URL of the image.
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    /**
     * Get initials from the menu name.
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
        }
        if (strlen($initials) < 2) {
            $initials = strtoupper(substr($this->name, 0, 2));
        }
        return $initials;
    }

    /**
     * Get human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return [
            'available' => 'Available',
            'low'       => 'Low Stock',
            'out'       => 'Out of Stock',
        ][$this->status] ?? $this->status;
    }

    /**
     * Get human-readable category label.
     */
    public function getCategoryLabelAttribute(): string
    {
        return [
            'food'       => 'Food',
            'drink'      => 'Drink',
            'snack'      => 'Snack',
            'additional' => 'Additional',
        ][$this->category] ?? $this->category;
    }

    // ========== MUTATORS ==========

    /**
     * Set the image attribute (supports base64 from modal).
     */
    public function setImageAttribute($value): void
    {
        if ($value && is_string($value) && str_starts_with($value, 'data:image')) {
            $this->attributes['image'] = $value;
        } else {
            $this->attributes['image'] = $value;
        }
    }

    // ========== BUSINESS LOGIC ==========

    /**
     * Decrease the stock of the linked product variant.
     * Also updates the menu item status based on remaining stock.
     *
     * @param int $quantity
     * @return bool
     */
    public function decrementStock(int $quantity = 1): bool
    {
        $variant = $this->productVariant;
        if (!$variant) {
            // If no variant linked, we cannot manage stock.
            return false;
        }

        $stock = $variant->stock;
        if (!$stock || $stock->current_stock < $quantity) {
            return false;
        }

        // Perform stock reduction inside a transaction
        \DB::transaction(function () use ($stock, $quantity, $variant) {
            $before = $stock->current_stock;
            $after = $before - $quantity;

            $stock->update([
                'current_stock'    => $after,
                'last_updated_at' => now(),
            ]);

            // Record movement
            $variant->stockMovements()->create([
                'pic_id'        => auth()->id(), // or pass as parameter
                'movement_type' => 'sale',
                'qty'           => $quantity,
                'stock_before'  => $before,
                'stock_after'   => $after,
                'notes'         => "Sold via menu item: {$this->name}",
            ]);
        });

        // Update menu item status based on new stock
        $this->updateStatusBasedOnStock($stock->fresh()->current_stock);

        return true;
    }

    /**
     * Increase the stock of the linked product variant.
     * Also updates the menu item status.
     *
     * @param int $quantity
     * @return void
     */
    public function incrementStock(int $quantity = 1): void
    {
        $variant = $this->productVariant;
        if (!$variant) {
            return;
        }

        $stock = $variant->stock;
        if (!$stock) {
            return;
        }

        \DB::transaction(function () use ($stock, $quantity, $variant) {
            $before = $stock->current_stock;
            $after = $before + $quantity;

            $stock->update([
                'current_stock'    => $after,
                'last_updated_at' => now(),
            ]);

            // Record movement
            $variant->stockMovements()->create([
                'pic_id'        => auth()->id(),
                'movement_type' => 'purchase', // atau 'restock'
                'qty'           => $quantity,
                'stock_before'  => $before,
                'stock_after'   => $after,
                'notes'         => "Restocked via menu item: {$this->name}",
            ]);
        });

        $this->updateStatusBasedOnStock($stock->fresh()->current_stock);
    }

    /**
     * Update the menu item's status based on the given stock quantity.
     *
     * @param int $stockQty
     * @return void
     */
    protected function updateStatusBasedOnStock(int $stockQty): void
    {
        if ($stockQty <= 0) {
            $this->status = 'out';
        } elseif ($stockQty <= 5) {
            $this->status = 'low';
        } else {
            $this->status = 'available';
        }
        $this->save();
    }
}
