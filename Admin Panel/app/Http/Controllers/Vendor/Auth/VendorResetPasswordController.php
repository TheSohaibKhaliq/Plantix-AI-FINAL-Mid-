<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

/**
 * VendorResetPasswordController
 *
 * Handles the actual password reset for vendors using the 'vendors' broker.
 */
class VendorResetPasswordController extends Controller
{
    use ResetsPasswords;

    /** Redirect vendors to their dashboard after a successful reset. */
    protected $redirectTo = '/vendor/dashboard';

    public function __construct()
    {
        $this->middleware('guest:vendor');
    }

    /**
     * Show the password reset form.
     * Route: GET /vendor/password/reset/{token}
     */
    public function showResetForm(Request $request, string $token): View
    {
        return view('vendor.auth.password-reset', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    protected function broker(): \Illuminate\Auth\Passwords\PasswordBroker
    {
        return Password::broker('vendors_users');
    }

    protected function guard(): \Illuminate\Auth\SessionGuard
    {
        return auth()->guard('vendor');
    }
}
