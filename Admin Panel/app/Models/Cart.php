<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = ['user_id', 'session_id', 'vendor_id'];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function getSubtotalAttribute(): float
    {
        return (float) $this->items->sum(fn ($item) => $item->unit_price * $item->quantity);
    }

    public function getTotalItemsAttribute(): int
    {
        return (int) $this->items->sum('quantity');
    }
}
