<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class CustomerRegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:web');
    }

    public function showRegistrationForm(): View
    {
        return view('customer.signup');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => 'user',
            'active'   => true,
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('home')
                         ->with('success', 'Welcome to Plantix AI, ' . $user->name . '!');
    }
}
