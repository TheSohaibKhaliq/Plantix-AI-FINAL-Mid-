<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VendorWithdrawMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

/**
 * WithdrawMethodApiController (Store Panel API)
 *
 * Replaces Firestore vendor document stripe/paypal/razorpay/flutterwave key management in:
 *   - withdraw_method/index.blade.php
 *   - withdraw_method/create.blade.php
 *
 * The old pattern was:
 *   database.collection('users').doc(vendorId).update({ stripe_account_id: '...', paypal_email: '...' })
 *   Replaced by a dedicated vendor_withdraw_methods table with encrypted credentials.
 */
class WithdrawMethodApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    private function vendorId(): int
    {
        return Auth::user()->vendor_id
            ?? abort(403, 'No vendor account linked.');
    }

    /**
     * GET /api/withdraw-methods
     * Replaces: reading stripe_account_id / paypal_email / etc. from vendor doc
     */
    public function index(): JsonResponse
    {
        $methods = VendorWithdrawMethod::where('vendor_id', $this->vendorId())
            ->get()
            ->map(fn($m) => $this->safeMethod($m));

        return response()->json($methods);
    }

    /**
     * POST /api/withdraw-methods
     */
    public function store(Request $request): JsonResponse
    {
        $vendorId = $this->vendorId();

        $request->validate([
            'method_type'   => 'required|in:stripe,paypal,razorpay,flutterwave,bank_transfer',
            'account_name'  => 'nullable|string|max:200',
            'account_detail'=> 'required|string|max:500',
        ]);

        $existing = VendorWithdrawMethod::where('vendor_id', $vendorId)
            ->where('method_type', $request->method_type)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'A ' . $request->method_type . ' method already exists. Update it instead.'], 422);
        }

        $method = VendorWithdrawMethod::create([
            'vendor_id'      => $vendorId,
            'method_type'    => $request->method_type,
            'account_name'   => $request->account_name,
            'account_detail' => Crypt::encryptString($request->account_detail),
        ]);

        return response()->json($this->safeMethod($method), 201);
    }

    /**
     * PUT /api/withdraw-methods/{method}
     */
    public function update(Request $request, VendorWithdrawMethod $method): JsonResponse
    {
        $this->authorizeMethod($method);

        $request->validate([
            'account_name'  => 'nullable|string|max:200',
            'account_detail'=> 'required|string|max:500',
        ]);

        $method->update([
            'account_name'   => $request->account_name,
            'account_detail' => Crypt::encryptString($request->account_detail),
        ]);

        return response()->json($this->safeMethod($method));
    }

    /**
     * DELETE /api/withdraw-methods/{method}
     * Replaces: database.collection('users').doc(id).update({ stripe_account_id: FieldValue.delete() })
     */
    public function destroy(VendorWithdrawMethod $method): JsonResponse
    {
        $this->authorizeMethod($method);
        $method->delete();
        return response()->json(null, 204);
    }

    /** Return method data WITHOUT decrypting the credentials. */
    private function safeMethod(VendorWithdrawMethod $method): array
    {
        return [
            'id'           => $method->id,
            'vendor_id'    => $method->vendor_id,
            'method_type'  => $method->method_type,
            'account_name' => $method->account_name,
            'created_at'   => $method->created_at?->toIso8601String(),
            'updated_at'   => $method->updated_at?->toIso8601String(),
        ];
    }

    private function authorizeMethod(VendorWithdrawMethod $method): void
    {
        if ($method->vendor_id !== $this->vendorId()) {
            abort(403, 'Unauthorized.');
        }
    }
}
