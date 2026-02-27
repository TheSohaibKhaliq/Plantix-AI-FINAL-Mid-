<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentReschedule extends Model
{
    protected $fillable = [
        'appointment_id',
        'requested_by',
        'original_scheduled_at',
        'proposed_scheduled_at',
        'reason',
        'status',
        'responded_at',
    ];

    protected $casts = [
        'original_scheduled_at' => 'datetime',
        'proposed_scheduled_at' => 'datetime',
        'responded_at'          => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
