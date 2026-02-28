<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class CustomerResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = '/';

    public function showResetForm(Request $request, string $token): View
    {
        return view('customer.password-reset', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    protected function broker(): \Illuminate\Auth\Passwords\PasswordBroker
    {
        return Password::broker('users');
    }
}
