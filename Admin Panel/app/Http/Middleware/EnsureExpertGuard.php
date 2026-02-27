<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureExpertGuard
 *
 * Applied to all /expert/* protected routes.
 * Verifies authentication via the 'expert' guard, that the user holds the
 * 'expert' role, the account is active, and the expert profile is approved.
 */
class EnsureExpertGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth('expert')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->route('expert.login')
                ->withErrors(['email' => 'Please sign in to the Expert Panel.']);
        }

        $user = auth('expert')->user();

        // Verify role
        if (! in_array($user->role, ['expert', 'agency_expert'])) {
            auth('expert')->logout();

            return redirect()->route('expert.login')
                ->withErrors(['email' => 'This area is for registered experts only.']);
        }

        // Verify account active
        if (! $user->active) {
            auth('expert')->logout();

            return redirect()->route('expert.login')
                ->withErrors(['email' => 'Your account has been disabled. Contact support.']);
        }

        // Verify expert profile exists and is approved
        $expert = $user->expert;
        if (! $expert || ! $expert->profile || $expert->profile->approval_status !== 'approved') {
            auth('expert')->logout();

            return redirect()->route('expert.login')
                ->withErrors(['email' => 'Your expert profile is pending admin approval.']);
        }

        // Share expert entity with all Blade templates in expert scope
        view()->share('currentExpert', $expert);
        view()->share('expertUser', $user);

        return $next($request);
    }
}
