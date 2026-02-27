<?php

namespace App\Policies\Expert;

use App\Models\ForumThread;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * ExpertForumPolicy
 *
 * Gates around expert participation in forum threads.
 */
class ExpertForumPolicy
{
    use HandlesAuthorization;

    /**
     * Experts can reply to open, approved threads.
     */
    public function reply(User $user, ForumThread $thread): bool
    {
        return $user->expert !== null
            && $user->expert->isApproved()
            && $thread->is_approved
            && ! $thread->is_locked;
    }

    /**
     * Experts can view any approved thread.
     */
    public function view(User $user, ForumThread $thread): bool
    {
        return $thread->is_approved;
    }
}
