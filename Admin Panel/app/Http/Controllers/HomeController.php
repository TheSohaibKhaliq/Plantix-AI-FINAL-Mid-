<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Admin dashboard — protected by EnsureAdminGuard route middleware.
     * Do NOT add $this->middleware('auth') here; that would check the
     * web guard instead of the admin guard and cause a redirect loop.
     */
    public function index()
    {
        return view('admin.home');
    }

    public function welcome()
    {
        return view('customer.welcome');
    }

    public function dashboard()
    {
        return view('admin.home');
    }

    public function users()
    {
        return view('admin.settings.users.index');
    }
}
