<?php

namespace App\Policies;

use App\Models\ReturnRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReturnRequestPolicy
{
    use HandlesAuthorization;

    public function before(User $user): ?bool
    {
        if ($user->isAdmin()) return true;
        return null;
    }

    public function view(User $user, ReturnRequest $return): bool
    {
        return $return->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isUser();
    }
}
