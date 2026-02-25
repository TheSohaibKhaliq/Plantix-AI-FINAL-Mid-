<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role',
        'active', 'is_document_verified', 'vendor_id',
        'wallet_amount', 'fcm_token', 'profile_photo',
        'must_reset_password', 'role_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at'    => 'datetime',
        'active'               => 'boolean',
        'is_document_verified' => 'boolean',
        'must_reset_password'  => 'boolean',
        'wallet_amount'        => 'decimal:2',
    ];

    // -------------------------------------------------------------------------
    // Role helpers
    // -------------------------------------------------------------------------

    public function isAdmin(): bool  { return $this->role === 'admin'; }
    public function isVendor(): bool { return $this->role === 'vendor'; }
    public function isDriver(): bool { return $this->role === 'driver'; }
    public function isUser(): bool   { return $this->role === 'user'; }

    /** Check a named permission from the admin-panel role system */
    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin() && !$this->role_id) {
            return true; // Super admin has all permissions
        }

        $perms = json_decode(session('user_permissions', '[]'), true);
        return in_array($permission, (array) $perms);
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function adminRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /** The vendor store this user owns (vendor role) */
    public function vendor(): HasOne
    {
        return $this->hasOne(Vendor::class, 'author_id');
    }

    /** All orders placed by this user (customer role) */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    /** Orders this driver is assigned to */
    public function drivenOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'driver_id');
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class, 'user_id')->latest();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'user_id');
    }

    public function favouriteVendors(): BelongsToMany
    {
        return $this->belongsToMany(Vendor::class, 'favourite_vendors')
                    ->withTimestamps();
    }

    public function favouriteProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'favourite_products')
                    ->withTimestamps();
    }

    public function bookedTables(): HasMany
    {
        return $this->hasMany(BookedTable::class, 'user_id');
    }
}
