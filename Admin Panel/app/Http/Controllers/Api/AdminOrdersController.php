<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class AdminOrdersController extends Controller
{
    /**
     * Get recent orders
     */
    public function recent(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            $orders = Order::where('status', '!=', 'Order Completed')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->with(['vendor', 'items'])
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'vendor_id' => $order->vendor_id,
                        'vendor' => $order->vendor ? [
                            'id' => $order->vendor->id,
                            'title' => $order->vendor->title ?? $order->vendor->name,
                        ] : null,
                        'total_amount' => $order->total_amount ?? 0,
                        'status' => $order->status,
                        'products' => $order->items ?? [],
                        'admin_commission' => $order->admin_commission ?? 0,
                        'discount' => $order->discount ?? 0,
                        'tax_amount' => $order->tax_amount ?? 0,
                        'delivery_charge' => $order->delivery_charge ?? 0,
                        'tip_amount' => $order->tip_amount ?? 0,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching orders: ' . $e->getMessage()
            ], 500);
        }
    }
}
