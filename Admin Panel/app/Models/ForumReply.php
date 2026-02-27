<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ForumReply extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'thread_id', 'user_id', 'body', 'is_approved',
        'is_expert_reply', 'expert_id',
    ];

    protected $casts = [
        'is_approved'    => 'boolean',
        'is_expert_reply'=> 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function thread(): BelongsTo
    {
        return $this->belongsTo(ForumThread::class, 'thread_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function expert(): BelongsTo
    {
        return $this->belongsTo(Expert::class);
    }

    public function expertResponse(): HasOne
    {
        return $this->hasOne(ForumExpertResponse::class, 'forum_reply_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeExpertReplies($query)
    {
        return $query->where('is_expert_reply', true);
    }
}
