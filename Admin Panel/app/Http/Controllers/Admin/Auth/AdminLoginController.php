<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * AdminLoginController
 *
 * Handles authentication for the Admin Panel using the 'admin' guard.
 * Only users with role admin or staff are permitted.
 */
class AdminLoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/admin/dashboard';

    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    public function showLoginForm(): View
    {
        return view('admin.auth.login');
    }

    protected function guard(): \Illuminate\Auth\SessionGuard
    {
        return auth()->guard('admin');
    }

    /**
     * After credentials pass; verify role before granting access.
     */
    protected function authenticated(Request $request, $user): RedirectResponse
    {
        if (! in_array($user->role, ['admin', 'staff'])) {
            $this->guard()->logout();
            throw ValidationException::withMessages([
                $this->username() => ['You do not have admin access.'],
            ]);
        }

        if (! $user->active) {
            $this->guard()->logout();
            throw ValidationException::withMessages([
                $this->username() => ['Your account has been disabled.'],
            ]);
        }

        // Load role and permissions into session for PermissionMiddleware & CheckUserRoleMiddleware
        $roleName    = null;
        $permissions = [];

        if ($user->role === 'admin' && ! $user->role_id) {
            // Super-admin: wildcard marker
            $roleName    = 'Super Admin';
            $permissions = ['*'];
        } elseif ($user->role_id) {
            $roleRow  = \Illuminate\Support\Facades\DB::table('role')->where('id', $user->role_id)->first();
            $roleName = $roleRow?->role_name ?? $user->role;

            // Collect both group labels and individual permission names
            $base = \Illuminate\Support\Facades\DB::table('permissions')
                ->join('role_permissions', 'permissions.id', '=', 'role_permissions.permission_id')
                ->where('role_permissions.role_id', $user->role_id)
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
            'admin_user_id'     => $user->id,
        ]);

        return redirect()->intended($this->redirectPath());
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
