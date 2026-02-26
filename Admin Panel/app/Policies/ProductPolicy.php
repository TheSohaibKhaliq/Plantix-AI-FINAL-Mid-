<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    /** Admins bypass all policy checks. */
    public function before(User $user): ?bool
    {
        if ($user->isAdmin()) return true;
        return null;
    }

    public function viewAny(User $user): bool
    {
        return true; // Public shop listing
    }

    public function view(User $user, Product $product): bool
    {
        return $product->is_active || $user->isAdmin() || $user->isVendor();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isVendor();
    }

    public function update(User $user, Product $product): bool
    {
        if ($user->isAdmin())  return true;
        if ($user->isVendor()) return $product->vendor_id === $user->vendor?->id;
        return false;
    }

    public function delete(User $user, Product $product): bool
    {
        if ($user->isAdmin())  return true;
        if ($user->isVendor()) return $product->vendor_id === $user->vendor?->id;
        return false;
    }
}
