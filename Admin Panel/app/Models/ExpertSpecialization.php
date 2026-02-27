<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpertSpecialization extends Model
{
    protected $fillable = ['expert_id', 'name', 'level'];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function expert(): BelongsTo
    {
        return $this->belongsTo(Expert::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeByLevel($query, string $level)
    {
        return $query->where('level', $level);
    }
}
