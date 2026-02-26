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

        // Load permissions into session for PermissionMiddleware
        $permissions = [];
        if ($user->role_id) {
            $permissions = \App\Models\Permission::whereHas('roles', fn ($q) => $q->where('role.id', $user->role_id))
                                                 ->pluck('name')
                                                 ->toArray();
        }
        session(['user_permissions' => json_encode($permissions)]);

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
