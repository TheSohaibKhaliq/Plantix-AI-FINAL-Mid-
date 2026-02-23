<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftCard extends Model
{
    protected $table = 'gift_cards';

    protected $fillable = [
        'code', 'amount', 'remaining_amount', 'purchased_by',
        'redeemed_by', 'redeemed_at', 'expires_at', 'is_active', 'firebase_doc_id',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'redeemed_at'      => 'datetime',
        'expires_at'       => 'datetime',
        'is_active'        => 'boolean',
    ];

    public function purchasedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'purchased_by');
    }

    public function redeemedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'redeemed_by');
    }

    public function isValid(): bool
    {
        return $this->is_active
            && $this->remaining_amount > 0
            && (!$this->expires_at || now()->lt($this->expires_at))
            && !$this->redeemed_at;
    }
}
