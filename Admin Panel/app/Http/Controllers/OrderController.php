<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\NotificationService;
use App\Services\OrderService;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly OrderService             $orderService,
        private readonly NotificationService      $notifications,
    ) {
        $this->middleware('auth');
    }

    public function index(string $id = ''): \Illuminate\View\View
    {
        return view('orders.index')->with('id', $id);
    }

    public function edit(string $id): \Illuminate\View\View
    {
        return view('orders.edit')->with('id', $id);
    }

    public function orderprint(string $id): \Illuminate\View\View
    {
        return view('orders.print')->with('id', $id);
    }

    // -------------------------------------------------------------------------
    // API endpoints called by Blade JS (replaces direct Firestore calls)
    // -------------------------------------------------------------------------

    /**
     * GET /orders/list
     * Server-side DataTables / paginated order list.
     */
    public function list(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'payment_status', 'vendor_id', 'user_id', 'date_from', 'date_to', 'search']);

        $paginated = $this->orders->paginated($filters, 20);

        return response()->json([
            'data'            => $paginated->items(),
            'recordsTotal'    => $paginated->total(),
            'recordsFiltered' => $paginated->total(),
        ]);
    }

    /**
     * POST /orders/status
     * Update order status and send FCM push notification.
     * Replaces the inline Firebase sendNotification curl code.
     */
    public function updateStatus(Request $request): JsonResponse
    {
        $request->validate([
            'orderId' => 'required|exists:orders,id',
            'status'  => 'required|string',
        ]);

        $order = Order::with('user', 'vendor')->findOrFail($request->orderId);

        try {
            $updated = $this->orderService->updateStatus($order, $request->status);

            // Send FCM push (non-blocking — runs after response via job)
            // But also send inline for immediate response
            $this->notifications->sendOrderStatusNotification(
                $updated->user,
                $updated->order_number,
                $updated->status,
                (string) $updated->id,
            );

            return response()->json([
                'success' => true,
                'message' => 'Order status updated and notification sent.',
                'order'   => $updated,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update order.'], 500);
        }
    }

    /**
     * POST /orders/notify
     * Send a manual push notification to a specific FCM token.
     * Replaces the legacy sendNotification() method that used raw curl.
     */
    public function sendNotification(Request $request): JsonResponse
    {
        $request->validate([
            'fcm'     => 'required|string',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ]);

        // Build a temporary user-like object with just the FCM token
        $target = new \App\Models\User(['fcm_token' => $request->fcm]);

        $sent = $this->notifications->sendToUser($target, $request->subject, $request->message);

        if ($sent) {
            return response()->json(['success' => true, 'message' => 'Notification successfully sent.']);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to send notification. Check Firebase credentials.',
        ], 500);
    }
}
