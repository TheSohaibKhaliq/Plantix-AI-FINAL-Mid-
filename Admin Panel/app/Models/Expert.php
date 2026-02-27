<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Expert extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'specialty', 'bio', 'avatar', 'is_available', 'hourly_rate',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'hourly_rate'  => 'decimal:2',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(ExpertProfile::class);
    }

    public function specializations(): HasMany
    {
        return $this->hasMany(ExpertSpecialization::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function forumResponses(): HasMany
    {
        return $this->hasMany(ForumExpertResponse::class);
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(ExpertNotificationLog::class);
    }

    // ── Convenience accessors ─────────────────────────────────────────────────

    public function getDisplayNameAttribute(): string
    {
        return $this->user->name ?? 'Expert';
    }

    public function getApprovalStatusAttribute(): string
    {
        return $this->profile->approval_status ?? 'pending';
    }

    public function isApproved(): bool
    {
        return $this->profile?->approval_status === 'approved';
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeApproved($query)
    {
        return $query->whereHas('profile', fn ($q) => $q->where('approval_status', 'approved'));
    }

    public function scopeAgencies($query)
    {
        return $query->whereHas('profile', fn ($q) => $q->where('account_type', 'agency'));
    }
}
