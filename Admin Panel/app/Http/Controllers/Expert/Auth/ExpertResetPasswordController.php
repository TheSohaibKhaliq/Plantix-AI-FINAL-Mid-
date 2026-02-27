<?php

namespace App\Http\Controllers\Expert\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

/**
 * ExpertResetPasswordController
 *
 * Handles the password reset form and processing for experts.
 * Uses the 'experts_users' broker and redirects back to expert login on success.
 */
class ExpertResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = '/expert/login';

    public function showResetForm(Request $request, $token = null): View
    {
        return view('expert.auth.passwords.reset', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    protected function guard(): \Illuminate\Auth\SessionGuard
    {
        return auth()->guard('expert');
    }

    protected function broker(): \Illuminate\Auth\Passwords\PasswordBroker
    {
        return Password::broker('experts_users');
    }
}
