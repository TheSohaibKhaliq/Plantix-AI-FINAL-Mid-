<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class AdminResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = '/admin/dashboard';

    public function showResetForm(Request $request, string $token): View
    {
        return view('admin.auth.passwords.reset', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    protected function broker(): \Illuminate\Auth\Passwords\PasswordBroker
    {
        return Password::broker('admins');
    }

    protected function guard(): \Illuminate\Auth\SessionGuard
    {
        return auth()->guard('admin');
    }
}
