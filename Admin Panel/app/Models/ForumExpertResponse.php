<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumExpertResponse extends Model
{
    protected $fillable = [
        'forum_reply_id',
        'expert_id',
        'is_expert_advice',
        'recommendation',
        'helpful_votes',
    ];

    protected $casts = [
        'is_expert_advice' => 'boolean',
        'helpful_votes'    => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function reply(): BelongsTo
    {
        return $this->belongsTo(ForumReply::class, 'forum_reply_id');
    }

    public function expert(): BelongsTo
    {
        return $this->belongsTo(Expert::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function incrementVotes(): void
    {
        $this->increment('helpful_votes');
    }
}
