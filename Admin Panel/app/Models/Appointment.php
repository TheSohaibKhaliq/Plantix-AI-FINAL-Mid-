<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'expert_id', 'scheduled_at', 'duration_minutes',
        'status', 'notes', 'admin_notes', 'fee', 'payment_status',
    ];

    protected $casts = [
        'scheduled_at'     => 'datetime',
        'fee'              => 'decimal:2',
        'duration_minutes' => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function expert(): BelongsTo
    {
        return $this->belongsTo(Expert::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePending($query)    { return $query->where('status', 'pending'); }
    public function scopeConfirmed($query)  { return $query->where('status', 'confirmed'); }
    public function scopeUpcoming($query)   { return $query->where('scheduled_at', '>=', now()); }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'warning',
            'confirmed' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            default     => 'secondary',
        };
    }
}
