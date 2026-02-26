<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureCustomerAuth
 *
 * Applied to authenticated-only frontend routes such as /orders, /checkout,
 * /account/profile, etc.
 *
 * Uses the default 'web' guard.
 */
class EnsureCustomerAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth('web')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('signin')
                ->with('intended', $request->url())
                ->withErrors(['email' => 'Please sign in to continue.']);
        }

        $user = auth('web')->user();

        if (! $user->active) {
            auth('web')->logout();
            return redirect()->route('signin')
                ->withErrors(['email' => 'Your account has been disabled.']);
        }

        view()->share('authUser', $user);

        return $next($request);
    }
}
