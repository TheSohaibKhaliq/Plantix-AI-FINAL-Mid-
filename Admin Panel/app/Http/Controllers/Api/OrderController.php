<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Services\OrderService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * OrderController (API)
 *
 * JSON endpoints consumed by Blade JS → replaces all direct Firestore
 * calls in orders/index.blade.php and related views.
 */
class OrderController extends Controller
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly OrderService             $orderService,
        private readonly NotificationService      $notifications,
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * GET /api/orders
     */
    public function index(Request $request): JsonResponse
    {
        $filters  = $request->only([
            'status', 'payment_status', 'vendor_id',
            'user_id', 'driver_id', 'date_from', 'date_to', 'search',
        ]);

        // Scope to vendor's own orders if requester is a vendor
        $user = auth()->user();
        if ($user->isVendor()) {
            $filters['vendor_id'] = $user->vendor?->id;
        } elseif ($user->isDriver()) {
            $filters['driver_id'] = $user->id;
        }

        $paginated = $this->orders->paginated($filters, (int) $request->get('per_page', 20));

        return response()->json([
            'data'         => $paginated->items(),
            'total'        => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
        ]);
    }

    /**
     * GET /api/orders/{id}
     */
    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        return response()->json($order->load(['user', 'vendor', 'driver', 'items', 'coupon']));
    }

    /**
     * PATCH /api/orders/{id}/status
     * Replaces the Firestore document update + FCM call pattern.
     */
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $this->authorize('updateStatus', $order);

        $request->validate([
            'status'    => 'required|string',
            'driver_id' => 'nullable|exists:users,id',
        ]);

        try {
            $updated = $this->orderService->updateStatus(
                $order,
                $request->status,
                $request->driver_id,
            );

            return response()->json([
                'success' => true,
                'order'   => $updated,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
