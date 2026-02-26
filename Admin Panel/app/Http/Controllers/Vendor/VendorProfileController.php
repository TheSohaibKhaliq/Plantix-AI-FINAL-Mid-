<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class VendorProfileController extends Controller
{
    // -------------------------------------------------------------------------
    // Show the vendor's profile page
    // -------------------------------------------------------------------------

    public function show(): View
    {
        /** @var User $user */
        $user   = auth('vendor')->user();
        $vendor = $user->vendor;

        return view('vendor.profile', compact('user', 'vendor'));
    }

    // -------------------------------------------------------------------------
    // Update user's personal info (name, phone, profile photo)
    // -------------------------------------------------------------------------

    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth('vendor')->user();

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'phone'         => ['nullable', 'string', 'max:30'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('profile_photo')) {
            // Delete old photo if stored in public disk
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $data['profile_photo'] = $request->file('profile_photo')
                ->store('profile-photos', 'public');
        } else {
            unset($data['profile_photo']);
        }

        $user->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }

    // -------------------------------------------------------------------------
    // Update vendor store info (title, description, address, etc.)
    // -------------------------------------------------------------------------

    public function updateStore(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user   = auth('vendor')->user();
        $vendor = $user->vendor;

        if (! $vendor) {
            return back()->withErrors(['store' => 'No store profile found.']);
        }

        $data = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string', 'max:2000'],
            'address'          => ['nullable', 'string', 'max:500'],
            'phone'            => ['nullable', 'string', 'max:30'],
            'open_time'        => ['nullable', 'date_format:H:i'],
            'close_time'       => ['nullable', 'date_format:H:i'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'delivery_fee'     => ['nullable', 'numeric', 'min:0'],
            'image'            => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'cover_photo'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
        ]);

        foreach (['image', 'cover_photo'] as $field) {
            if ($request->hasFile($field)) {
                if ($vendor->$field && Storage::disk('public')->exists($vendor->$field)) {
                    Storage::disk('public')->delete($vendor->$field);
                }
                $data[$field] = $request->file($field)->store('vendors', 'public');
            } else {
                unset($data[$field]);
            }
        }

        $vendor->update($data);

        return back()->with('success', 'Store information updated.');
    }

    // -------------------------------------------------------------------------
    // Change password
    // -------------------------------------------------------------------------

    public function changePassword(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth('vendor')->user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password changed successfully.');
    }
}
