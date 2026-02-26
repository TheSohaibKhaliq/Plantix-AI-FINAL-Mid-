<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumThread extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'forum_category_id', 'title', 'body',
        'is_pinned', 'is_locked', 'is_approved', 'views',
    ];

    protected $casts = [
        'is_pinned'   => 'boolean',
        'is_locked'   => 'boolean',
        'is_approved' => 'boolean',
        'views'       => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ForumCategory::class, 'forum_category_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ForumReply::class, 'thread_id');
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }
}
