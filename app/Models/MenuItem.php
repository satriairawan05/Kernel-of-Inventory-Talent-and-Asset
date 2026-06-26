<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'menu_items';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'name',
        'price',
        'category',
        'status',
        'image',
        'stock',
    ];

    /**
     * Relasi BelongsTo from MenuItem to Company
     *
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    // ========== ACCESSORS ==========
    public function getImageUrlAttribute()
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    public function getInitialsAttribute()
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

    public function getStatusLabelAttribute()
    {
        return [
            'available' => 'Available',
            'low' => 'Low Stock',
            'out' => 'Out of Stock',
        ][$this->status] ?? $this->status;
    }

    public function getCategoryLabelAttribute()
    {
        return [
            'food' => 'Food',
            'drink' => 'Drink',
            'snack' => 'Snack',
            'additional' => 'Additional',
        ][$this->category] ?? $this->category;
    }

    // ========== MUTATORS ==========
    public function setImageAttribute($value)
    {
        if ($value && is_string($value) && str_starts_with($value, 'data:image')) {
            // Handle base64 image (dari modal)
            $this->attributes['image'] = $value;
        } else {
            $this->attributes['image'] = $value;
        }
    }

    // ========== HELPERS ==========
    public function decrementStock(int $quantity = 1)
    {
        if ($this->stock >= $quantity) {
            $this->stock -= $quantity;
            $this->save();

            if ($this->stock == 0) {
                $this->status = 'out';
                $this->save();
            } elseif ($this->stock <= 5) {
                $this->status = 'low';
                $this->save();
            }

            return true;
        }
        return false;
    }

    public function incrementStock(int $quantity = 1)
    {
        $this->stock += $quantity;
        $this->save();

        if ($this->stock > 5 && $this->status !== 'available') {
            $this->status = 'available';
            $this->save();
        }
    }
}
