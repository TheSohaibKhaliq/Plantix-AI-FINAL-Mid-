<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReturnRequest extends Model
{
    use SoftDeletes;

    protected $table = 'returns';

    protected $fillable = [
        'order_id', 'user_id', 'return_reason_id',
        'description', 'status', 'admin_notes', 'images',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reason(): BelongsTo
    {
        return $this->belongsTo(ReturnReason::class, 'return_reason_id');
    }

    public function refund(): HasOne
    {
        return $this->hasOne(Refund::class, 'return_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePending($query) { return $query->where('status', 'pending'); }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isApproved(): bool  { return $this->status === 'approved'; }
}
