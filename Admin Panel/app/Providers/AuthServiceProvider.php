<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\User;
use App\Models\Vendor;
use App\Policies\OrderPolicy;
use App\Policies\UserPolicy;
use App\Policies\VendorPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Vendor::class => VendorPolicy::class,
        Order::class  => OrderPolicy::class,
        User::class   => UserPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Super-admin bypasses all Gate checks
        Gate::before(function (User $user, string $ability): ?bool {
            if ($user->isAdmin() && !$user->role_id) {
                return true;
            }
            return null; // let normal policy evaluation continue
        });
    }
}
