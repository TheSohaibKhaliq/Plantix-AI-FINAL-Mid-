<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class CustomerAuthController extends Controller
{
    // ── Register ──────────────────────────────────────────────────────────────

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone'    => 'nullable|string|max:30',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'phone'    => $data['phone'] ?? null,
            'role'     => 'user',
            'active'   => true,
        ]);

        $token = $user->createToken('plantix-customer')->plainTextToken;

        return response()->json([
            'success' => true,
            'token'   => $token,
            'user'    => $this->userPayload($user),
        ], 201);
    }

    // ── Login ──────────────────────────────────────────────────────────────────

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->where('role', 'user')->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['email' => 'Invalid credentials.']);
        }

        if (! $user->active) {
            return response()->json(['message' => 'Your account has been disabled.'], 403);
        }

        // Revoke previous customer tokens before issuing a new one
        $user->tokens()->where('name', 'plantix-customer')->delete();

        $token = $user->createToken('plantix-customer')->plainTextToken;

        return response()->json([
            'success' => true,
            'token'   => $token,
            'user'    => $this->userPayload($user),
        ]);
    }

    // ── Logout ──────────────────────────────────────────────────────────────────

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['success' => true, 'message' => 'Logged out.']);
    }

    // ── Get current user ─────────────────────────────────────────────────────

    public function user(Request $request): JsonResponse
    {
        return response()->json(['success' => true, 'user' => $this->userPayload($request->user())]);
    }

    // ── Update profile ───────────────────────────────────────────────────────

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name'    => 'sometimes|string|max:100',
            'phone'   => 'sometimes|nullable|string|max:30',
            'address' => 'sometimes|nullable|string|max:500',
        ]);

        $user->update(array_filter($data, fn ($v) => $v !== null));

        return response()->json(['success' => true, 'user' => $this->userPayload($user->fresh())]);
    }

    // ── Forgot password ──────────────────────────────────────────────────────

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker()->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['success' => true, 'message' => 'Password reset link sent.']);
        }

        return response()->json(['success' => false, 'message' => 'Unable to send reset link.'], 422);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function userPayload(User $user): array
    {
        return [
            'id'      => $user->id,
            'name'    => $user->name,
            'email'   => $user->email,
            'phone'   => $user->phone,
            'role'    => $user->role,
            'active'  => $user->active,
        ];
    }
}
