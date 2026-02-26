<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ReturnReason;
use App\Services\ReturnRefundService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerOrderController extends Controller
{
    public function __construct(
        private readonly ReturnRefundService $returnService,
    ) {}

    public function index(): View
    {
        $user   = auth('web')->user();
        $orders = Order::with(['vendor', 'items.product'])
                       ->forCustomer($user->id)
                       ->latest()
                       ->paginate(10);

        return view('pages.orders', compact('orders'));
    }

    public function show(int $id): View
    {
        $user  = auth('web')->user();
        $order = Order::with(['vendor', 'items.product', 'statusHistory', 'returnRequest', 'refund'])
                      ->forCustomer($user->id)
                      ->findOrFail($id);

        return view('pages.order-details', compact('order'));
    }

    public function success(int $id): View
    {
        $user  = auth('web')->user();
        $order = Order::with(['vendor', 'items.product'])
                      ->forCustomer($user->id)
                      ->findOrFail($id);

        return view('pages.order-success', compact('order'));
    }

    public function requestReturn(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'return_reason_id' => 'nullable|exists:return_reasons,id',
            'description'      => 'required|string|max:1000',
        ]);

        $user  = auth('web')->user();
        $order = Order::forCustomer($user->id)
                      ->where('status', 'delivered')
                      ->findOrFail($id);

        $this->returnService->requestReturn($user, $order, $request->validated());

        return back()->with('success', 'Return request submitted. Admin will review it shortly.');
    }
}
