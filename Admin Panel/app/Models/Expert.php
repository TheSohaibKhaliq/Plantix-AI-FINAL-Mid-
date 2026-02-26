<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }
}
