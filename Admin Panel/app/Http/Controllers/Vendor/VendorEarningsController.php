<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payout;
use App\Models\PayoutRequest;
use App\Models\WalletTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorEarningsController extends Controller
{
    // -------------------------------------------------------------------------
    // Earnings & payout overview
    // -------------------------------------------------------------------------

    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user   = auth('vendor')->user();
        $vendor = $user->vendor;

        if (! $vendor) {
            abort(403, 'No vendor profile found.');
        }

        // ── Summary stats ────────────────────────────────────────────────────
        $totalEarnings   = Order::forVendor($vendor->id)
                                ->where('payment_status', 'paid')
                                ->sum('total');

        $monthEarnings   = Order::forVendor($vendor->id)
                                ->where('payment_status', 'paid')
                                ->whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->sum('total');

        $pendingPayout   = PayoutRequest::where('vendor_id', $vendor->id)
                                        ->where('status', 'pending')
                                        ->sum('amount');

        $totalPaidOut    = PayoutRequest::where('vendor_id', $vendor->id)
                                        ->where('status', 'completed')
                                        ->sum('amount');

        $walletBalance   = $user->wallet_amount;

        $stats = compact(
            'totalEarnings', 'monthEarnings',
            'pendingPayout', 'totalPaidOut', 'walletBalance'
        );

        // ── Monthly earnings chart (last 6 months) ───────────────────────────
        $monthlyChart = collect(range(5, 0))->map(function ($offset) use ($vendor) {
            $date = now()->subMonths($offset);
            return [
                'month' => $date->format('M Y'),
                'total' => Order::forVendor($vendor->id)
                                ->where('payment_status', 'paid')
                                ->whereMonth('created_at', $date->month)
                                ->whereYear('created_at', $date->year)
                                ->sum('total'),
            ];
        });

        // ── Recent payout requests ────────────────────────────────────────────
        $payoutRequests = PayoutRequest::where('vendor_id', $vendor->id)
                                       ->latest()
                                       ->paginate(15, ['*'], 'payout_page');

        // ── Wallet transaction history ────────────────────────────────────────
        $walletHistory  = WalletTransaction::where('user_id', $user->id)
                                           ->latest()
                                           ->limit(20)
                                           ->get();

        return view('vendor.earnings', compact(
            'vendor', 'stats', 'monthlyChart',
            'payoutRequests', 'walletHistory'
        ));
    }

    // -------------------------------------------------------------------------
    // Request payout
    // -------------------------------------------------------------------------

    public function requestPayout(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user   = auth('vendor')->user();
        $vendor = $user->vendor;

        $data = $request->validate([
            'amount'       => ['required', 'numeric', 'min:1'],
            'method'       => ['required', 'string', 'in:bank,paypal,stripe,wallet'],
            'bank_details' => ['nullable', 'string', 'max:1000'],
            'notes'        => ['nullable', 'string', 'max:500'],
        ]);

        // Ensure requested amount doesn't exceed wallet balance
        if ((float) $data['amount'] > (float) $user->wallet_amount) {
            return back()->withErrors([
                'amount' => 'Requested amount exceeds your available wallet balance.',
            ]);
        }

        // Store bank details + notes in admin_note for the admin to review
        $adminNote = '';
        if (! empty($data['bank_details'])) {
            $adminNote .= 'Bank/Payment Details: ' . $data['bank_details'];
        }
        if (! empty($data['notes'])) {
            $adminNote .= ($adminNote ? ' | ' : '') . 'Note: ' . $data['notes'];
        }

        PayoutRequest::create([
            'vendor_id'  => $vendor->id,
            'amount'     => $data['amount'],
            'method'     => $data['method'],
            'admin_note' => $adminNote ?: null,
            'status'     => 'pending',
        ]);

        return back()->with('success', 'Payout request submitted. Admin will process it shortly.');
    }
}
