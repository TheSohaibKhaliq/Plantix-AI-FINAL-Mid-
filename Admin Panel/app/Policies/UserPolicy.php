<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function view(User $currentUser, User $targetUser): bool
    {
        return $currentUser->isAdmin() || $currentUser->id === $targetUser->id;
    }

    public function update(User $currentUser, User $targetUser): bool
    {
        return $currentUser->isAdmin() || $currentUser->id === $targetUser->id;
    }

    public function delete(User $currentUser, User $targetUser): bool
    {
        // Prevent self-deletion
        return $currentUser->isAdmin() && $currentUser->id !== $targetUser->id;
    }

    public function toggleActive(User $currentUser, User $targetUser): bool
    {
        return $currentUser->isAdmin() && $currentUser->id !== $targetUser->id;
    }
}
