<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\NotificationService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * StoreOrderController (Store Panel API)
 *
 * Replaces all Firebase Firestore JS calls in:
 *   - orders/index.blade.php
 *   - orders/edit.blade.php
 *   - orders/placed.blade.php
 *   - orders/accepted.blade.php
 *   - orders/rejected.blade.php
 *   - OrderController::sendnotification() (raw curl FCM → NotificationService)
 */
class StoreOrderController extends Controller
{
    public function __construct(
        private readonly OrderService        $orderService,
        private readonly NotificationService $notificationService,
    ) {
        $this->middleware('auth:sanctum');
    }

    private function vendorId(): int
    {
        return Auth::user()->vendor_id
            ?? abort(403, 'No vendor account linked.');
    }

    /**
     * GET /api/orders
     * Supports ?status=placed|accepted|rejected|completed&skip=0&limit=20
     *
     * Replaces: database.collection('orders').where('vendor_id','==',id)...get()
     */
    public function index(Request $request): JsonResponse
    {
        $vendorId = $this->vendorId();

        $orders = Order::where('vendor_id', $vendorId)
            ->with(['items.product', 'user:id,name,phone,fcm_token'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->from,   fn($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($request->to,     fn($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->latest()
            ->skip((int) $request->get('skip', 0))
            ->take((int) $request->get('limit', 20))
            ->get();

        return response()->json($orders);
    }

    /**
     * GET /api/orders/{order}
     * Replaces: database.collection('orders').doc(id).get()
     */
    public function show(Order $order): JsonResponse
    {
        $this->authorizeOrder($order);
        return response()->json($order->load(['items.product', 'user', 'driver', 'coupon']));
    }

    /**
     * PATCH /api/orders/{order}/status
     * Body: { status: 'accepted' | 'rejected' | 'cooking' | 'on_the_way' | 'delivered' }
     *
     * Replaces: database.collection('orders').doc(id).update({status:...})
     */
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $this->authorizeOrder($order);

        $request->validate([
            'status' => 'required|in:accepted,rejected,cooking,ready,on_the_way,delivered,cancelled',
        ]);

        $updated = $this->orderService->updateStatus($order, $request->status, Auth::user());

        return response()->json($updated->load(['items.product', 'user']));
    }

    /**
     * GET /api/orders/{order}/print
     * Returns structured order data for the print view.
     */
    public function printData(Order $order): JsonResponse
    {
        $this->authorizeOrder($order);
        return response()->json($order->load(['items.product', 'user', 'vendor']));
    }

    /**
     * POST /api/orders/{order}/notification
     * Body: { subject: '...', message: '...', fcm?: '...' }
     *
     * Replaces the raw curl FCM code in the old OrderController::sendnotification()
     */
    public function sendNotification(Request $request, Order $order): JsonResponse
    {
        $this->authorizeOrder($order);

        $request->validate([
            'subject' => 'required|string|max:100',
            'message' => 'required|string|max:500',
            'fcm'     => 'nullable|string',
        ]);

        $token = $request->get('fcm') ?: $order->user?->fcm_token;

        if (empty($token)) {
            return response()->json(['success' => false, 'message' => 'No FCM token available.'], 422);
        }

        $this->notificationService->sendToUser(
            $token,
            $request->subject,
            $request->message,
            ['order_id' => (string) $order->id, 'type' => 'order_status'],
        );

        return response()->json(['success' => true, 'message' => 'Notification sent.']);
    }

    private function authorizeOrder(Order $order): void
    {
        if ($order->vendor_id !== $this->vendorId() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized.');
        }
    }
}
