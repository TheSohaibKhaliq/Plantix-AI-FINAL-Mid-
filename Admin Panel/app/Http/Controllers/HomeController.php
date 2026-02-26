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
        return view('home');
    }

    public function welcome()
    {
        return view('welcome');
    }

    public function dashboard()
    {
        return view('home');
    }

    public function users()
    {
        return view('settings.users.index');
    }
}
