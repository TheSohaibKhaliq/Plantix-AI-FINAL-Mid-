<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * CheckUserRoleMiddleware — Admin Panel RBAC session seeder
 *
 * Runs on every web request.  When an admin-guard user is authenticated
 * it loads their role name and all permission slugs + group names into
 * the session so PermissionMiddleware can check them cheaply (no extra
 * DB queries per route).
 *
 * Session keys written (admin guard only):
 *   admin_role        — human-readable role name, e.g. "Super Admin"
 *   admin_permissions — JSON-encoded array of permission slugs/groups
 *   admin_user_id     — prevents stale data for a different user
 *
 * Non-admin guards (vendor, expert, web/customer) are intentionally
 * skipped — they use their own guard middleware, not RBAC.
 */
class CheckUserRoleMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $adminUser = auth('admin')->user();

        if ($adminUser) {
            $sessionKey = 'admin_user_id';

            // Only reload when the session is stale or user changed
            if (! session()->has('admin_permissions') || session($sessionKey) !== $adminUser->id) {

                $roleName    = null;
                $permissions = [];

                if ($adminUser->role === 'admin' && ! $adminUser->role_id) {
                    // Super-admin: wildcard marker — PermissionMiddleware short-circuits
                    $roleName    = 'Super Admin';
                    $permissions = ['*'];
                } elseif ($adminUser->role_id) {
                    $roleRow  = DB::table('role')->where('id', $adminUser->role_id)->first();
                    $roleName = $roleRow?->role_name ?? $adminUser->role;

                    // Collect both group labels and individual permission names
                    $base = DB::table('permissions')
                        ->join('role_permissions', 'permissions.id', '=', 'role_permissions.permission_id')
                        ->where('role_permissions.role_id', $adminUser->role_id)
                        ->select('permissions.name', 'permissions.group')
                        ->get();

                    $permissions = $base->pluck('name')
                        ->merge($base->pluck('group'))
                        ->filter()
                        ->unique()
                        ->values()
                        ->toArray();
                }

                session([
                    'admin_role'        => $roleName,
                    'admin_permissions' => json_encode($permissions),
                    $sessionKey         => $adminUser->id,
                ]);
            }
        }

        return $next($request);
    }
}