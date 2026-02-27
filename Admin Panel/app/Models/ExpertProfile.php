<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpertProfile extends Model
{
    protected $fillable = [
        'expert_id',
        'agency_name',
        'specialization',
        'experience_years',
        'certifications',
        'availability_schedule',
        'website',
        'linkedin',
        'contact_phone',
        'city',
        'country',
        'account_type',
        'approval_status',
        'admin_notes',
        'approved_at',
    ];

    protected $casts = [
        'experience_years'      => 'integer',
        'approved_at'           => 'datetime',
        'availability_schedule' => 'array',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function expert(): BelongsTo
    {
        return $this->belongsTo(Expert::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    public function isSuspended(): bool
    {
        return $this->approval_status === 'suspended';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->approval_status) {
            'approved'  => 'success',
            'pending'   => 'warning',
            'rejected'  => 'danger',
            'suspended' => 'secondary',
            default     => 'light',
        };
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }
}
