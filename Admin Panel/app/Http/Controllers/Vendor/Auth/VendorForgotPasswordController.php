<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

/**
 * VendorForgotPasswordController
 *
 * Sends a password-reset link to a vendor's email address.
 * Uses the 'vendors' password broker defined in config/auth.php.
 */
class VendorForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    public function __construct()
    {
        $this->middleware('guest:vendor');
    }

    /**
     * Show the form to request a password reset link.
     * Route: GET /vendor/password/forgot
     */
    public function showLinkRequestForm(): View
    {
        return view('vendor.auth.password-forgot');
    }

    /**
     * Use the 'vendors' broker so the reset token goes to the vendor guard's
     * user model (User with role=vendor), not the default 'users' broker.
     */
    protected function broker(): \Illuminate\Auth\Passwords\PasswordBroker
    {
        return Password::broker('vendors_users');
    }
}
