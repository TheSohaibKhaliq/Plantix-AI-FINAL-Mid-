<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * PermissionMiddleware — Admin Panel ONLY
 *
 * This middleware enforces RBAC exclusively for the Admin Panel.
 * It MUST only be applied to routes under /admin/* which are already
 * protected by EnsureAdminGuard (the 'admin' middleware alias).
 *
 * Usage in routes:
 *   ->middleware('permission:stores,stores.edit')
 *
 * Arguments:
 *   $group  — permission group name (e.g. 'stores')
 *   $name   — specific permission slug (e.g. 'stores.edit')
 *
 * Super-admins (role === 'admin' with no role_id) bypass ALL checks.
 * Staff users (role === 'staff' with role_id set) are checked against
 * the permissions cached in the session by CheckUserRoleMiddleware.
 */
class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, ?string $group = null, ?string $name = null): Response
    {
        // Authenticate via the Admin guard – never fall back to web guard
        $user = auth('admin')->user();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('admin.login');
        }

        // Super-admin shortcut — role=admin with no role_id means full access
        if ($user->role === 'admin' && ! $user->role_id) {
            return $next($request);
        }

        // Staff must have an assigned role
        if (! $user->role_id) {
            abort(403, 'No role assigned. Contact your administrator.');
        }

        // Permissions are loaded into the session by CheckUserRoleMiddleware
        // at login time (keyed as JSON array of permission slugs + groups).
        $permissions = json_decode(session('admin_permissions', '[]'), true);

        if ($group && ! in_array($group, $permissions, true)) {
            abort(403, "Access denied — missing permission group: {$group}");
        }

        if ($name && ! in_array($name, $permissions, true)) {
            abort(403, "Access denied — missing permission: {$name}");
        }

        return $next($request);
    }
}