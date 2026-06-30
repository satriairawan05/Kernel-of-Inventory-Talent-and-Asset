<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_id',
        'menu_item_id',
        'name',
        'price',
        'qty',
        'subtotal',
        'discount_per_item',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price'             => 'integer',
        'qty'               => 'integer',
        'subtotal'          => 'integer',
        'discount_per_item' => 'integer',
    ];

    // ============================================================
    // RELATIONS
    // ============================================================

    /**
     * Get the transaction that owns the item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get the menu item that owns the transaction item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    // ============================================================
    // MODEL EVENTS
    // ============================================================

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::saving(function ($item) {
            // Auto-calculate subtotal when price or qty changes
            if ($item->price !== null && $item->qty !== null) {
                $item->subtotal = $item->price * $item->qty;
            }
        });
    }
}