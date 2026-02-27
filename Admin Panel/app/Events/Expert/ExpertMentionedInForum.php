<?php

namespace App\Events\Expert;

use App\Models\Expert;
use App\Models\ForumReply;
use App\Models\ForumThread;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a user mentions @expert or when farmer replies to an expert thread.
 */
class ExpertMentionedInForum
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Expert      $expert,
        public readonly ForumThread $thread,
        public readonly ForumReply  $reply,
    ) {}
}
