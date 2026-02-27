<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    use SoftDeletes;

    // Full lifecycle statuses
    public const STATUS_REQUESTED    = 'requested';
    public const STATUS_PENDING      = 'pending';
    public const STATUS_ACCEPTED     = 'accepted';
    public const STATUS_CONFIRMED    = 'confirmed';
    public const STATUS_REJECTED     = 'rejected';
    public const STATUS_RESCHEDULED  = 'rescheduled';
    public const STATUS_COMPLETED    = 'completed';
    public const STATUS_CANCELLED    = 'cancelled';

    public const VALID_STATUSES = [
        self::STATUS_REQUESTED,
        self::STATUS_PENDING,
        self::STATUS_ACCEPTED,
        self::STATUS_CONFIRMED,
        self::STATUS_REJECTED,
        self::STATUS_RESCHEDULED,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
    ];

    protected $fillable = [
        'user_id', 'expert_id', 'scheduled_at', 'duration_minutes',
        'status', 'notes', 'admin_notes', 'fee', 'payment_status',
        'topic', 'meeting_link',
        'reschedule_requested_at', 'accepted_at', 'rejected_at',
        'completed_at', 'reject_reason',
    ];

    protected $casts = [
        'scheduled_at'              => 'datetime',
        'reschedule_requested_at'   => 'datetime',
        'accepted_at'               => 'datetime',
        'rejected_at'               => 'datetime',
        'completed_at'              => 'datetime',
        'fee'                       => 'decimal:2',
        'duration_minutes'          => 'integer',
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

    public function statusHistory(): HasMany
    {
        return $this->hasMany(AppointmentStatusHistory::class)->orderBy('changed_at', 'asc');
    }

    public function reschedules(): HasMany
    {
        return $this->hasMany(AppointmentReschedule::class)->orderBy('created_at', 'desc');
    }

    public function latestReschedule(): HasOne
    {
        return $this->hasOne(AppointmentReschedule::class)->latestOfMany();
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePending($query)       { return $query->where('status', self::STATUS_PENDING); }
    public function scopeRequested($query)     { return $query->where('status', self::STATUS_REQUESTED); }
    public function scopeAccepted($query)      { return $query->where('status', self::STATUS_ACCEPTED); }
    public function scopeConfirmed($query)     { return $query->where('status', self::STATUS_CONFIRMED); }
    public function scopeRejected($query)      { return $query->where('status', self::STATUS_REJECTED); }
    public function scopeRescheduled($query)   { return $query->where('status', self::STATUS_RESCHEDULED); }
    public function scopeCompleted($query)     { return $query->where('status', self::STATUS_COMPLETED); }
    public function scopeCancelled($query)     { return $query->where('status', self::STATUS_CANCELLED); }
    public function scopeUpcoming($query)      { return $query->where('scheduled_at', '>=', now()); }
    public function scopeForExpert($query, int $expertId) { return $query->where('expert_id', $expertId); }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_REQUESTED   => 'primary',
            self::STATUS_PENDING     => 'warning',
            self::STATUS_ACCEPTED    => 'info',
            self::STATUS_CONFIRMED   => 'info',
            self::STATUS_REJECTED    => 'danger',
            self::STATUS_RESCHEDULED => 'secondary',
            self::STATUS_COMPLETED   => 'success',
            self::STATUS_CANCELLED   => 'dark',
            default                  => 'light',
        };
    }

    public function isActive(): bool
    {
        return in_array($this->status, [
            self::STATUS_REQUESTED,
            self::STATUS_PENDING,
            self::STATUS_ACCEPTED,
            self::STATUS_CONFIRMED,
            self::STATUS_RESCHEDULED,
        ]);
    }

    public function canBeAccepted(): bool
    {
        return in_array($this->status, [self::STATUS_REQUESTED, self::STATUS_PENDING]);
    }

    public function canBeRejected(): bool
    {
        return $this->canBeAccepted();
    }

    public function canBeRescheduled(): bool
    {
        return in_array($this->status, [
            self::STATUS_ACCEPTED,
            self::STATUS_CONFIRMED,
        ]);
    }

    public function canBeCompleted(): bool
    {
        return in_array($this->status, [
            self::STATUS_ACCEPTED,
            self::STATUS_CONFIRMED,
        ]);
    }
}
