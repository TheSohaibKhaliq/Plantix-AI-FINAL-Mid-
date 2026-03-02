<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * VendorRegisterController
 *
 * Registers new vendor users and creates a corresponding vendor store record.
 * Vendors are set to inactive by default and require admin approval.
 */
class VendorRegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/vendor/login';

    public function __construct()
    {
        $this->middleware('guest:vendor');
    }

    public function showRegistrationForm(): View
    {
        return view('vendor.auth.register');
    }

    protected function validator(array $data): \Illuminate\Validation\Validator
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'store_name' => ['required', 'string', 'max:255', 'unique:vendors,title'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'regex:/^(\+92|0)?3[0-9]{2}[0-9]{7}$/', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['required', 'accepted'],
        ], [
            'phone.regex' => 'Please enter a valid Pakistani phone number (e.g., 03001234567).',
            'store_name.unique' => 'This store name is already registered.',
        ]);
    }

    protected function create(array $data): User
    {
        // Use transaction to ensure both user and vendor are created together
        return DB::transaction(function () use ($data) {
            // Create user account
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'role' => 'vendor',
                'active' => false,  // Require admin approval
            ]);

            // Create corresponding vendor store record
            Vendor::create([
                'author_id' => $user->id,
                'title' => $data['store_name'],
                'is_active' => false,
                'is_approved' => false,
            ]);

            return $user;
        });
    }

    public function register(Request $request): RedirectResponse
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        return redirect()->route('vendor.registration.success')->with('message', 'Registration successful! Awaiting admin approval.');
    }

    public function registrationSuccess(): View
    {
        return view('vendor.auth.registration-success');
    }
}

