<?php

namespace App\Http\Controllers\Expert\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

/**
 * ExpertForgotPasswordController
 *
 * Sends a password reset link via SMTP to the expert's registered email.
 * Uses the 'experts_users' password broker defined in config/auth.php.
 */
class ExpertForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    public function showLinkRequestForm(): View
    {
        return view('expert.auth.passwords.email');
    }

    protected function broker(): \Illuminate\Auth\Passwords\PasswordBroker
    {
        return Password::broker('experts_users');
    }
}
