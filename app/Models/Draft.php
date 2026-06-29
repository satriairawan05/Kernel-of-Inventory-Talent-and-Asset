<?php

namespace App\Models;

use App\Enums\DraftStatusEnum;
use App\Enums\DraftTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Draft extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'type',
        'table_number',
        'name',
        'status',
        'subtotal',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'table_number' => 'integer',
        'type' => DraftTypeEnum::class,
        'status' => DraftStatusEnum::class,
    ];

    /* ============================================
       RELATIONSHIPS
       ============================================ */

    /**
     * Get the company that owns this draft.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the items for this draft.
     */
    public function items()
    {
        return $this->hasMany(DraftItem::class);
    }

    /* ============================================
       BUSINESS LOGIC
       ============================================ */

    /**
     * Recalculate subtotal from items directly from database.
     */
    public function recalculate(): void
    {
        $this->subtotal = $this->items()->sum('total');
        $this->save();
    }

    /**
     * Mark the draft as processing (when moved to cart).
     */
    public function markAsProcessing(): void
    {
        $this->status = DraftStatusEnum::PROCESSING;
        $this->save();
    }

    /**
     * Mark the draft as completed (when checkout is done).
     */
    public function markAsCompleted(): void
    {
        $this->status = DraftStatusEnum::COMPLETED;
        $this->save();
    }

    /**
     * Check if the draft is active.
     */
    public function isActive(): bool
    {
        return $this->status === DraftStatusEnum::ACTIVE;
    }

    /**
     * Check if the draft is processing.
     */
    public function isProcessing(): bool
    {
        return $this->status === DraftStatusEnum::PROCESSING;
    }

    /**
     * Check if the draft is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === DraftStatusEnum::COMPLETED;
    }

    /* ============================================
       SCOPES
       ============================================ */

    /**
     * Scope a query to only include active drafts.
     */
    public function scopeActive($query)
    {
        return $query->where('status', DraftStatusEnum::ACTIVE->value);
    }

    /**
     * Scope a query to only include processing drafts.
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', DraftStatusEnum::PROCESSING->value);
    }

    /**
     * Scope a query to only include completed drafts.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', DraftStatusEnum::COMPLETED->value);
    }

    /**
     * Scope a query to only include drafts of a specific company.
     */
    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope a query to only include drafts of a specific type.
     */
    public function scopeOfType($query, DraftTypeEnum $type)
    {
        return $query->where('type', $type->value);
    }
}
