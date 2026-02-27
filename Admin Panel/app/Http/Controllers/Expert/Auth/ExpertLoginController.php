<?php

namespace App\Http\Controllers\Expert\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * ExpertLoginController
 *
 * Handles authentication for the Expert Panel using the 'expert' guard.
 * Only users with role 'expert' or 'agency_expert' are permitted.
 * Expert profile must be approved by admin before granting access.
 */
class ExpertLoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/expert/dashboard';

    public function __construct()
    {
        $this->middleware('guest:expert')->except('logout');
    }

    public function showLoginForm(): View
    {
        return view('expert.auth.login');
    }

    protected function guard(): \Illuminate\Auth\SessionGuard
    {
        return auth()->guard('expert');
    }

    /**
     * After credentials pass; verify role and approval status.
     */
    protected function authenticated(Request $request, $user): RedirectResponse
    {
        if (! in_array($user->role, ['expert', 'agency_expert'])) {
            $this->guard()->logout();
            throw ValidationException::withMessages([
                $this->username() => ['This area is restricted to registered experts.'],
            ]);
        }

        if (! $user->active) {
            $this->guard()->logout();
            throw ValidationException::withMessages([
                $this->username() => ['Your account has been disabled. Contact support.'],
            ]);
        }

        $expert = $user->expert;
        if (! $expert || ! $expert->profile || $expert->profile->approval_status !== 'approved') {
            $this->guard()->logout();
            throw ValidationException::withMessages([
                $this->username() => ['Your expert profile is pending admin approval. You will be notified by email.'],
            ]);
        }

        return redirect()->intended($this->redirectPath());
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('expert.login');
    }
}
