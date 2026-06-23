<?php

namespace App\Models;

use App\Models\Shift;
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
        'use_inventory'
    ];

    /**
     * Get all shifts belonging to this company.
     */
    public function shifts(): HasMany
    {
        return $this->hasMany(
            Shift::class,
            'company_id',
            'id'
        );
    }

    /**
     * Get the categories for the company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categories(): HasMany
    {
        return $this->hasMany(
            Category::class,
            'category_id',
            'id'
        );
    }

    /**
     * Get the company logo URL attribute.
     *
     * @return string
     */
    public function getLogoUrlAttribute(): string
    {
        return $this->company_logo
            ? asset(
                'storage/' . $this->company_logo
            )
            : asset(
                'assets/img/icons/av color.png'
            );
    }
}
