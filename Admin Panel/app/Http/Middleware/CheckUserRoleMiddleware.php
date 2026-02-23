<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Models\Permission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * CheckUserRoleMiddleware
 *
 * Runs on every authenticated request.
 * Loads the user's role name and all permission names into the session
 * so that PermissionMiddleware can check them cheaply without extra queries.
 */
class CheckUserRoleMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Only reload permissions if session is empty or user role changed
            if (!session()->has('user_permissions') || session('user_id') !== $user->id) {

                $roleName    = null;
                $permissions = [];

                if ($user->role === 'admin' && !$user->role_id) {
                    // Super-admin gets a wildcard marker
                    $roleName    = 'Super Admin';
                    $permissions = ['*'];
                } elseif ($user->role_id) {
                    $roleRow  = DB::table('role')->where('id', $user->role_id)->first();
                    $roleName = $roleRow?->role_name ?? $user->role;

                    // Load both group names and specific permission names
                    $permissions = DB::table('permissions')
                        ->join('role_permissions', 'permissions.id', '=', 'role_permissions.permission_id')
                        ->where('role_permissions.role_id', $user->role_id)
                        ->pluck('permissions.name')
                        ->merge(
                            DB::table('permissions')
                                ->join('role_permissions', 'permissions.id', '=', 'role_permissions.permission_id')
                                ->where('role_permissions.role_id', $user->role_id)
                                ->pluck('permissions.group')
                        )
                        ->unique()
                        ->values()
                        ->toArray();
                }

                session([
                    'user_role'        => $roleName,
                    'user_permissions' => json_encode($permissions),
                    'user_id'          => $user->id,
                ]);
            }
        }

        return $next($request);
    }
}