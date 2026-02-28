<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payout;

class AdminPayoutsController extends Controller
{
    /**
     * Get recent payouts
     */
    public function recent(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            $payouts = Payout::where('payment_status', 'Success')
                ->orWhere('payment_status', 'Paid')
                ->orWhere('status', 'Success')
                ->orderBy('paid_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->with(['vendor'])
                ->get()
                ->map(function ($payout) {
                    return [
                        'id' => $payout->id,
                        'vendor_id' => $payout->vendor_id,
                        'vendor_name' => $payout->vendor ? ($payout->vendor->title ?? $payout->vendor->name) : 'Unknown',
                        'amount' => $payout->amount ?? 0,
                        'payment_method' => $payout->payment_method ?? 'Bank Transfer',
                        'status' => $payout->payment_status ?? $payout->status ?? 'Success',
                        'paid_date' => $payout->paid_date ?? $payout->created_at,
                        'transaction_id' => $payout->transaction_id ?? $payout->id,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $payouts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching payouts: ' . $e->getMessage()
            ], 500);
        }
    }
}
