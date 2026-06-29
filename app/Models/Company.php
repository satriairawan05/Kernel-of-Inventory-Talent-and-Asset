<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'companies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_name',
        'company_email',
        'company_phone',
        'company_address',
        'company_logo',
        'bussiness_type',
        'use_menu',
        'use_service',
        'use_inventory',
    ];

    /**
     * Get all cash summaries for this company.
     */
    public function cashSummaries(): HasMany
    {
        return $this->hasMany(CashSummary::class);
    }

    /**
     * Get all shifts belonging to this company.
     */
    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }

    /**
     * Get the categories for the company.
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Get all menu items for this company.
     */
    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    public function drafts(): HasMany
    {
        return $this->hasMany(Draft::class);
    }

    /**
     * Get the company logo URL attribute.
     */
    public function getLogoUrlAttribute(): string
    {
        return $this->company_logo
            ? asset('storage/' . $this->company_logo)
            : asset('assets/img/icons/av color.png');
    }
}
