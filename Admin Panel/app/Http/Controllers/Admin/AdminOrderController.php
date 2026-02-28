<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Services\Shared\CartCheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminOrderController extends Controller
{
    public function __construct(
        private readonly CartCheckoutService $checkout,
    ) {}

    public function index(Request $request): View
    {
        $query = Order::with(['user', 'vendor', 'items'])
                      ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$request->search}%")
                                                     ->orWhere('email', 'like', "%{$request->search}%"));
            });
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        $orders = $query->paginate(20)->withQueryString();

        $statuses = ['pending','accepted','preparing','ready','driver_assigned','picked_up','delivered','rejected','cancelled'];

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    public function show(int $id): View
    {
        $order = Order::with([
            'user', 'vendor', 'driver', 'coupon',
            'items.product', 'statusHistory.changedBy', 'returnRequest.reason', 'refund',
        ])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,accepted,preparing,ready,driver_assigned,picked_up,delivered,rejected,cancelled',
            'notes'  => 'nullable|string|max:500',
        ]);

        $order = Order::findOrFail($id);
        /** @var \App\Models\User $admin */
        $admin = auth('admin')->user();

        $this->checkout->updateStatus($order, $request->status, $request->notes, $admin);

        return back()->with('success', "Order #{$order->order_number} status updated to {$request->status}.");
    }

    public function assignDriver(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'driver_id' => 'required|exists:users,id',
        ]);

        $order = Order::findOrFail($id);
        $driver = User::where('id', $request->driver_id)->where('role', 'driver')->firstOrFail();

        $order->update([
            'driver_id' => $driver->id,
            'status'    => 'driver_assigned',
        ]);

        return back()->with('success', "Driver {$driver->name} assigned.");
    }
}

