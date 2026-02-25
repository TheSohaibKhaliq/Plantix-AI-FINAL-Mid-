<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'user_id', 'vendor_id', 'product_id', 'order_id',
        'rating', 'comment', 'is_active',
    ];

    protected $casts = [
        'rating'    => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        // Recalculate vendor rating after save/delete
        $recalc = fn(Review $r) => $r->vendor?->recalculateRating();
        static::saved($recalc);
        static::deleted($recalc);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
