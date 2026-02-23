<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use App\Models\PayoutRequest;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * StorePayoutController (Store Panel API)
 *
 * Replaces Firestore payouts/wallet JS calls in:
 *   - restaurants_payouts/index.blade.php
 *   - restaurants_payouts/create.blade.php
 *   - wallettransaction.blade.php
 */
class StorePayoutController extends Controller
{
    public function __construct(private readonly WalletService $walletService)
    {
        $this->middleware('auth:sanctum');
    }

    private function vendorId(): int
    {
        return Auth::user()->vendor_id
            ?? abort(403, 'No vendor account linked.');
    }

    /**
     * GET /api/payouts
     * Replaces: database.collection('payouts').where('vendor_id','==',id).get()
     */
    public function index(Request $request): JsonResponse
    {
        $vendorId = $this->vendorId();

        $payouts = Payout::where('vendor_id', $vendorId)
            ->latest()
            ->skip((int) $request->get('skip', 0))
            ->take((int) $request->get('limit', 50))
            ->get();

        $pendingRequests = PayoutRequest::where('vendor_id', $vendorId)
            ->where('status', 'pending')
            ->get();

        return response()->json([
            'payouts'          => $payouts,
            'pending_requests' => $pendingRequests,
            'wallet_balance'   => Auth::user()->wallet_amount ?? 0,
        ]);
    }

    /**
     * POST /api/payouts/request
     * Replaces: database.collection('payout_requests').add({...})
     */
    public function requestPayout(Request $request): JsonResponse
    {
        $vendorId = $this->vendorId();

        $request->validate([
            'amount'            => 'required|numeric|min:1',
            'withdraw_method_id'=> 'required|exists:vendor_withdraw_methods,id',
            'note'              => 'nullable|string|max:300',
        ]);

        $vendor = Auth::user()->vendor;
        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found.'], 404);
        }

        if ($vendor->wallet_amount < $request->amount) {
            return response()->json(['message' => 'Insufficient wallet balance.'], 422);
        }

        $payoutRequest = PayoutRequest::create([
            'vendor_id'          => $vendorId,
            'amount'             => $request->amount,
            'withdraw_method_id' => $request->withdraw_method_id,
            'note'               => $request->note,
            'status'             => 'pending',
        ]);

        return response()->json($payoutRequest, 201);
    }

    /**
     * GET /api/wallet-transactions
     * Replaces: database.collection('wallet').where('vendor_id','==',id).get()
     */
    public function walletTransactions(Request $request): JsonResponse
    {
        $vendorId = $this->vendorId();

        $transactions = WalletTransaction::where('vendor_id', $vendorId)
            ->latest()
            ->skip((int) $request->get('skip', 0))
            ->take((int) $request->get('limit', 50))
            ->get();

        return response()->json($transactions);
    }
}
