<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * RedirectIfAuthenticated
 *
 * Redirects already-authenticated users to their panel's home page
 * so they don't land on login pages when already signed in.
 *
 * Each guard maps to its dedicated panel HOME path:
 *   admin  → RouteServiceProvider::ADMIN_HOME
 *   vendor → RouteServiceProvider::VENDOR_HOME
 *   expert → RouteServiceProvider::EXPERT_HOME
 *   web    → RouteServiceProvider::HOME  (customer/public)
 */
class RedirectIfAuthenticated
{
    /** Guard → HOME path mapping */
    private const GUARD_HOMES = [
        'admin'  => RouteServiceProvider::ADMIN_HOME,
        'vendor' => RouteServiceProvider::VENDOR_HOME,
        'expert' => RouteServiceProvider::EXPERT_HOME,
    ];

    public function handle(Request $request, Closure $next, string ...$guards): mixed
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $home = self::GUARD_HOMES[$guard] ?? RouteServiceProvider::HOME;
                return redirect($home);
            }
        }

        return $next($request);
    }
}
