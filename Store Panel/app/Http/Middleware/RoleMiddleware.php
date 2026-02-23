<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RoleMiddleware
 *
 * Usage in routes:
 *   ->middleware('role:vendor')
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!in_array($user->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden.'], 403);
            }
            abort(403, 'You do not have permission to access this area.');
        }

        if (!$user->active) {
            auth()->logout();
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Account disabled.'], 403);
            }
            return redirect()->route('login')->withErrors(['email' => 'Your account has been disabled.']);
        }

        return $next($request);
    }
}
