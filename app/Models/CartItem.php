<?php
// app/Models/CartItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'menu_item_id',
        'name',
        'price',
        'qty',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'qty' => 'integer',
        'subtotal' => 'decimal:2',
    ];

    // ===== RELATIONSHIPS =====

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    // ===== HELPERS =====

    /**
     * Check if this is an additional item (not linked to menu).
     */
    public function isAdditional(): bool
    {
        return $this->menu_item_id === null;
    }

    /**
     * Get formatted price with Rupiah.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get formatted subtotal.
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    /**
     * Get the subtotal (already stored as generated column, but this is a fallback).
     */
    public function getSubtotalAttribute($value)
    {
        // Jika value null (misal karena migration belum dijalankan), hitung manual
        if ($value === null) {
            return $this->price * $this->qty;
        }
        return $value;
    }
}