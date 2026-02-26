<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureAdminGuard
 *
 * Applied to all /admin/* routes.
 * Verifies the user is authenticated via the 'admin' guard AND carries the
 * admin or staff role.  Inactive accounts are rejected immediately.
 */
class EnsureAdminGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth('admin')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Please sign in to the Admin Panel.']);
        }

        $user = auth('admin')->user();

        if (! in_array($user->role, ['admin', 'staff'])) {
            auth('admin')->logout();
            return redirect()->route('admin.login')
                ->withErrors(['email' => 'You do not have admin access.']);
        }

        if (! $user->active) {
            auth('admin')->logout();
            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Your account has been disabled.']);
        }

        // Push current user to view so layouts can reference it easily
        view()->share('adminUser', $user);

        return $next($request);
    }
}
