<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * After credentials are validated, verify the user is a vendor and is active.
     * This replaces the Firebase Auth + role check that previously happened client-side.
     */
    protected function authenticated(Request $request, $user): mixed
    {
        if (!in_array($user->role, ['vendor', 'admin'])) {
            auth()->logout();

            throw ValidationException::withMessages([
                $this->username() => [
                    trans('auth.vendor_only'),
                ],
            ]);
        }

        if (!$user->active) {
            auth()->logout();

            throw ValidationException::withMessages([
                $this->username() => [
                    trans('auth.account_disabled'),
                ],
            ]);
        }

        return redirect()->intended($this->redirectPath());
    }

    public function forgotPassword(): mixed
    {
        if (\Auth::check()) {
            return redirect(route('profile'));
        }

        return view('auth.forgot_password');
    }
}
