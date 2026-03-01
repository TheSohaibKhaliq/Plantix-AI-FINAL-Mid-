<?php

namespace App\Services\Vendor;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Vendor;
use App\Services\Shared\CartCheckoutService;
use App\Services\Shared\StockService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

/**
 * Vendor Order Service
 * Section 4 – Vendor Flow: Order Receiving & Status Update Logic
 *
 * Wraps CartCheckoutService with vendor-scoped logic.
 * Enforces valid vendor-side state transitions.
 */
class VendorOrderService
{
    /** Transitions a vendor is allowed to trigger */
    private const VENDOR_TRANSITIONS = [
        'pending'    => ['confirmed', 'cancelled'],
        'confirmed'  => ['processing', 'cancelled'],
        'processing' => ['shipped'],
        'shipped'    => [], // auto or admin/customer confirms delivery
    ];

    public function __construct(
        private readonly CartCheckoutService $checkout,
        private readonly StockService        $stock,
    ) {}

    /**
     * Get paginated orders scoped to this vendor.
     *
     * @param Vendor      $vendor
     * @param array       $filters  ['status' => ..., 'search' => ..., 'date_from' => ..., 'date_to' => ...]
     * @param int         $perPage
     * @return LengthAwarePaginator
     */
    public function getOrders(Vendor $vendor, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Order::with(['user', 'items.product', 'payment'])
            ->where('vendor_id', $vendor->id);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', $search)
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $search));
            });
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Update an order's status as a vendor.
     * Validates both the transition rules AND that the order belongs to this vendor.
     *
     * @param  Vendor       $vendor
     * @param  int          $orderId
     * @param  string       $newStatus
     * @param  string|null  $notes
     * @param  string|null  $trackingNumber   Required when transitioning to 'shipped'
     * @return Order
     * @throws ValidationException|\InvalidArgumentException
     */
    public function updateStatus(
        Vendor  $vendor,
        int     $orderId,
        string  $newStatus,
        ?string $notes          = null,
        ?string $trackingNumber = null,
    ): Order {
        $order = Order::with('items.product')
            ->where('vendor_id', $vendor->id)
            ->findOrFail($orderId);

        $allowed = self::VENDOR_TRANSITIONS[$order->status] ?? [];

        if (! in_array($newStatus, $allowed)) {
            throw new \InvalidArgumentException(
                "Vendor cannot transition order from '{$order->status}' to '{$newStatus}'."
            );
        }

        // Shipping requires a tracking reference
        if ($newStatus === 'shipped' && empty($trackingNumber) && empty($notes)) {
            throw ValidationException::withMessages([
                'tracking_number' => 'Please provide a tracking number or courier notes when marking as shipped.',
            ]);
        }

        $updateData = ['status' => $newStatus];
        if ($trackingNumber) {
            $updateData['tracking_number'] = $trackingNumber;
        }

        $order->update($updateData);

        // Restore stock on vendor-initiated cancellation
        if ($newStatus === 'cancelled') {
            foreach ($order->items as $item) {
                if ($item->product) {
                    $this->stock->restoreStock(
                        product:     $item->product,
                        qty:         $item->quantity,
                        reason:      'cancel',
                        orderId:     $order->id,
                        returnId:    null,
                        initiatedBy: $vendor->author_id,
                    );
                }
            }
        }

        OrderStatusHistory::create([
            'order_id'   => $order->id,
            'status'     => $newStatus,
            'notes'      => $notes,
            'changed_by' => $vendor->author_id,
        ]);

        // Notify customer
        try {
            $order->user->notify(new \App\Notifications\OrderStatusChangedNotification($order->fresh(), $newStatus));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning(
                'Vendor status-change notification failed: ' . $e->getMessage(),
                ['order_id' => $order->id]
            );
        }

        return $order->fresh();
    }
}
