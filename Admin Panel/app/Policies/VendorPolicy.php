<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vendor;

class VendorPolicy
{
    /** Admin can view any vendor; vendor can view their own */
    public function view(User $user, Vendor $vendor): bool
    {
        return $user->isAdmin() || $vendor->author_id === $user->id;
    }

    /** Admin can create vendors; vendor users cannot create new ones */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /** Admin can update any; vendor can only update their own */
    public function update(User $user, Vendor $vendor): bool
    {
        return $user->isAdmin() || $vendor->author_id === $user->id;
    }

    /** Only admin can delete */
    public function delete(User $user, Vendor $vendor): bool
    {
        return $user->isAdmin();
    }

    /** Only admin can approve/reject vendors */
    public function approve(User $user): bool
    {
        return $user->isAdmin();
    }
}
