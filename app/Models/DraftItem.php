<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DraftItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'draft_id',
        'menu_item_id',
        'name',
        'price',
        'qty',
        'total',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'qty' => 'integer',
    ];

    /* ============================================
       RELATIONSHIPS
       ============================================ */

    /**
     * Get the draft that owns this item.
     */
    public function draft()
    {
        return $this->belongsTo(Draft::class);
    }

    /**
     * Get the menu item that this draft item references (optional).
     */
    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    /* ============================================
       ELOQUENT EVENTS
       ============================================ */

    protected static function booted(): void
    {
        // Auto-calculate subtotal before saving
        static::saving(function ($item) {
            $item->total = $item->price * $item->qty;
        });

        static::saved(function ($item) {
            // Refresh draft to avoid stale relation data
            $draft = $item->draft()->first();
            if ($draft) {
                $draft->recalculate();
            }
        });

        static::deleted(function ($item) {
            $draft = $item->draft()->first();
            if ($draft) {
                $draft->recalculate();
            }
        });
    }
}
