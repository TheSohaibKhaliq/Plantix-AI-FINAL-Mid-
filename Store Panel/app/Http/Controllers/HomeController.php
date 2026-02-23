<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard with live stats from MySQL.
     * Replaces: Firestore JS queries in home.blade.php that fetched orders/products counts
     */
    public function index()
    {
        $user     = Auth::user();
        $vendorId = $user->vendor_id;

        if (!$vendorId) {
            return view('home')->with('stats', []);
        }

        $vendor = Vendor::findOrFail($vendorId);

        // Aggregate stats — replaces multiple Firestore collection queries
        $stats = [
            'vendor'             => $vendor,
            'total_orders'       => Order::where('vendor_id', $vendorId)->count(),
            'pending_orders'     => Order::where('vendor_id', $vendorId)->where('status', 'placed')->count(),
            'accepted_orders'    => Order::where('vendor_id', $vendorId)->where('status', 'accepted')->count(),
            'completed_orders'   => Order::where('vendor_id', $vendorId)->where('status', 'delivered')->count(),
            'total_products'     => Product::where('vendor_id', $vendorId)->count(),
            'active_products'    => Product::where('vendor_id', $vendorId)->where('active', true)->count(),
            'wallet_balance'     => $vendor->wallet_amount ?? 0,
            'today_revenue'      => Order::where('vendor_id', $vendorId)
                                        ->where('status', 'delivered')
                                        ->whereDate('created_at', today())
                                        ->sum('total_amount'),
            'monthly_revenue'    => Order::where('vendor_id', $vendorId)
                                        ->where('status', 'delivered')
                                        ->whereMonth('created_at', now()->month)
                                        ->whereYear('created_at', now()->year)
                                        ->sum('total_amount'),
            'recent_orders'      => Order::with('user:id,name')
                                        ->where('vendor_id', $vendorId)
                                        ->latest()
                                        ->take(5)
                                        ->get(),
        ];

        return view('home')->with('stats', $stats);
    }

    public function welcome()
    {
        return view('welcome');
    }

    public function dashboard()
    {
        return redirect()->route('home');
    }

    public function users()
    {
        return view('users');
    }
}

