<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\NotificationService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * OrderController (Store Panel — Web / Blade)
 *
 * These actions just return Blade views. The actual data is fetched by the
 * Blade JS layer calling the REST API (StoreOrderController) which replaced
 * all Firestore database.collection('orders')... calls.
 *
 * The sendnotification() method's raw curl FCM code has been replaced by
 * NotificationService delegation. It is kept here for the existing web route:
 *   POST /order-status-notification
 */
class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService        $orderService,
        private readonly NotificationService $notificationService,
    ) {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('orders.index');
    }

    public function edit(int $id)
    {
        return view('orders.edit', ['orderId' => $id]);
    }

    public function placedOrders()
    {
        return view('orders.placed');
    }

    public function acceptedOrders()
    {
        return view('orders.accepted');
    }

    public function rejectedOrders()
    {
        return view('orders.rejected');
    }

    public function orderprint(int $id)
    {
        $order = Order::with(['items.product', 'user', 'vendor'])->findOrFail($id);

        $vendorId = Auth::user()->vendor_id;
        if ($order->vendor_id !== $vendorId && !Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('orders.print', compact('order'));
    }

    /**
     * POST /order-status-notification
     *
     * Replaces the raw curl FCM block that was here previously.
     * Data flow:
     *   Blade JS → POST this route → NotificationService (Google_Client + FCM HTTP v1)
     */
    public function sendNotification(Request $request)
    {
        $request->validate([
            'orderStatus' => 'required|string',
            'subject'     => 'required|string|max:100',
            'message'     => 'required|string|max:500',
            'fcm'         => 'nullable|string',
        ]);

        $allowedStatuses = ['Order Accepted', 'Order Rejected', 'Order Completed'];
        if (!in_array($request->orderStatus, $allowedStatuses)) {
            return response()->json(['success' => false, 'message' => 'Notification not required for this status.']);
        }

        $token = $request->get('fcm');
        if (empty($token)) {
            return response()->json(['success' => false, 'message' => 'No FCM token provided.'], 422);
        }

        $this->notificationService->sendToUser(
            $token,
            $request->subject,
            $request->message,
            ['type' => 'order_status'],
        );

        return response()->json(['success' => true, 'message' => 'Notification successfully sent.']);
    }
}

