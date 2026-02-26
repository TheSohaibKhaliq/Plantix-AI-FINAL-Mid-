<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\View\View;

class VendorDashboardController extends Controller
{
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user   = auth('vendor')->user();
        $vendor = $user->vendor;

        if (! $vendor) {
            abort(403, 'No vendor profile found. Contact admin.');
        }

        $stats = [
            'total_orders'    => Order::forVendor($vendor->id)->count(),
            'pending_orders'  => Order::forVendor($vendor->id)->where('status', 'pending')->count(),
            'total_products'  => Product::where('vendor_id', $vendor->id)->count(),
            'low_stock'       => Product::where('vendor_id', $vendor->id)
                                        ->where('track_stock', true)
                                        ->where('stock_quantity', '<=', 5)
                                        ->count(),
            'today_revenue'   => Order::forVendor($vendor->id)
                                      ->whereDate('created_at', today())
                                      ->where('payment_status', 'paid')
                                      ->sum('total'),
            'month_revenue'   => Order::forVendor($vendor->id)
                                      ->whereMonth('created_at', now()->month)
                                      ->whereYear('created_at', now()->year)
                                      ->where('payment_status', 'paid')
                                      ->sum('total'),
        ];

        $recentOrders = Order::forVendor($vendor->id)
                             ->with(['user', 'items'])
                             ->latest()
                             ->limit(10)
                             ->get();

        return view('vendor.dashboard', compact('vendor', 'stats', 'recentOrders'));
    }
}
