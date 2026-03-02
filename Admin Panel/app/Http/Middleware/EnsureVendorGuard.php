<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureVendorGuard
 *
 * Applied to all /vendor/* routes.
 * Verifies authentication via the 'vendor' guard and that the user holds the
 * vendor role.  Also checks that the account is active.
 */
class EnsureVendorGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth('vendor')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('vendor.login')
                ->withErrors(['email' => 'Please sign in to the Vendor Panel.']);
        }

        $user = auth('vendor')->user();

        if ($user->role !== 'vendor') {
            auth('vendor')->logout();
            return redirect()->route('vendor.login')
                ->withErrors(['email' => 'This area is for vendors only.']);
        }

        if (! $user->active) {
            auth('vendor')->logout();
            return redirect()->route('vendor.login')
                ->withErrors(['email' => 'Your account is not active. Contact support.']);
        }

        // Also check the vendor record directly — handles cases where vendors.is_active
        // was flipped without touching users.active (suspension via admin panel).
        $vendor = $user->vendor;
        if ($vendor && ! $vendor->is_active) {
            auth('vendor')->logout();
            return redirect()->route('vendor.login')
                ->withErrors(['email' => 'Your vendor account has been suspended. Contact support.']);
        }
        view()->share('currentVendor', $vendor);
        view()->share('vendorUser', $user);

        return $next($request);
    }
}
