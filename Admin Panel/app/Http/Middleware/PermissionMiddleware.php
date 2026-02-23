<?php

namespace App\Http\Middleware;

use App\Models\Permission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * PermissionMiddleware (updated for MySQL-backed permissions)
 *
 * Usage in routes:
 *   ->middleware('permission:stores,stores.edit')
 *
 * Super-admins (role === 'admin' with no role_id) bypass all checks.
 */
class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, ?string $group = null, ?string $name = null): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Super-admin shortcut — no role_id assigned means full access
        if ($user->role === 'admin' && !$user->role_id) {
            return $next($request);
        }

        if (!$user->role_id) {
            abort(403, 'Unauthorised access.');
        }

        // Permissions cached by CheckUserRoleMiddleware at login
        $permissions = json_decode(session('user_permissions', '[]'), true);

        if ($group && !in_array($group, $permissions, true)) {
            abort(403, 'Unauthorised access.');
        }

        if ($name && !in_array($name, $permissions, true)) {
            abort(403, 'Unauthorised access.');
        }

        return $next($request);
    }
}