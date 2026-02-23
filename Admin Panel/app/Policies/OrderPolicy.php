<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /** Admin sees all; vendor sees orders for their store; user sees own orders */
    public function view(User $user, Order $order): bool
    {
        return $user->isAdmin()
            || $order->vendor?->author_id === $user->id
            || $order->user_id === $user->id
            || $order->driver_id === $user->id;
    }

    /** Status updates: admin always; vendor for their own orders; driver for assigned */
    public function updateStatus(User $user, Order $order): bool
    {
        if ($user->isAdmin()) return true;
        if ($order->vendor?->author_id === $user->id) return true;
        if ($user->isDriver() && $order->driver_id === $user->id) return true;
        return false;
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }
}
