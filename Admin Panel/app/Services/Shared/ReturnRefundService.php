<?php

namespace App\Services\Shared;

use App\Models\Order;
use App\Models\Refund;
use App\Models\ReturnRequest;
use App\Models\User;
use App\Notifications\ReturnStatusNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReturnRefundService
{
    /**
     * Customer submits a return request.
     */
    public function requestReturn(User $user, Order $order, array $data): ReturnRequest
    {
        // Prevent duplicate returns
        if ($order->returnRequest) {
            throw new \RuntimeException('A return request already exists for this order.');
        }

        $return = ReturnRequest::create([
            'order_id'         => $order->id,
            'user_id'          => $user->id,
            'return_reason_id' => $data['return_reason_id'] ?? null,
            'description'      => $data['description'] ?? null,
            'images'           => $data['images'] ?? null,
            'status'           => 'pending',
        ]);

        return $return->fresh(['order', 'reason']);
    }

    /**
     * Admin approves a return.
     */
    public function approve(ReturnRequest $return, ?string $adminNotes = null): ReturnRequest
    {
        $return->update(['status' => 'approved', 'admin_notes' => $adminNotes]);

        try {
            $return->user->notify(new ReturnStatusNotification($return));
        } catch (\Throwable $e) {
            Log::error('Return approval notification failed: ' . $e->getMessage());
        }

        return $return->fresh();
    }

    /**
     * Admin rejects a return.
     */
    public function reject(ReturnRequest $return, string $adminNotes): ReturnRequest
    {
        $return->update(['status' => 'rejected', 'admin_notes' => $adminNotes]);

        try {
            $return->user->notify(new ReturnStatusNotification($return));
        } catch (\Throwable $e) {
            Log::error('Return rejection notification failed: ' . $e->getMessage());
        }

        return $return->fresh();
    }

    /**
     * Process a refund for an approved return.
     */
    public function processRefund(ReturnRequest $return, array $data, User $processedBy): Refund
    {
        return DB::transaction(function () use ($return, $data, $processedBy) {

            $refund = Refund::create([
                'return_id'       => $return->id,
                'order_id'        => $return->order_id,
                'amount'          => $data['amount'],
                'method'          => $data['method'],
                'status'          => 'processed',
                'transaction_ref' => $data['transaction_ref'] ?? null,
                'notes'           => $data['notes'] ?? null,
                'processed_at'    => now(),
                'processed_by'    => $processedBy->id,
            ]);

            $return->update(['status' => 'refunded']);
            $return->order->update(['payment_status' => 'refunded']);

            // Wallet refund
            if ($data['method'] === 'wallet') {
                $return->user->increment('wallet_amount', $data['amount']);
            }

            return $refund;
        });
    }
}


