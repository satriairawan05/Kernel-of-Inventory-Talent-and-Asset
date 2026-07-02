<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'group_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ============================================================
    // RELATIONS
    // ============================================================

    /**
     * Get the company that the user belongs to.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the group that the user belongs to.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get all cashier sessions opened by this user.
     */
    public function cashierSessions(): HasMany
    {
        return $this->hasMany(CashierSession::class, 'user_id');
    }

    /**
     * Get all transactions made by this user.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    /**
     * Get all carts created by this user.
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class, 'user_id');
    }

    /**
     * Get all stock movements created by this user.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'pic_id');
    }

    // ============================================================
    // HELPERS
    // ============================================================

    /**
     * Check if user is admin (group_id = 1).
     */
    public function isAdmin(): bool
    {
        return $this->group_id === 1;
    }

    /**
     * Check if user is cashier (group_id = 2).
     */
    public function isCashier(): bool
    {
        return $this->group_id === 2;
    }

    /**
     * Check if user has a specific group.
     */
    public function hasGroup(int $groupId): bool
    {
        return $this->group_id === $groupId;
    }
}