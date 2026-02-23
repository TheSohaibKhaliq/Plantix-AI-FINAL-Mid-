<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;

class EarningController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * GET /earnings
     * Shows vendor earnings summary. Data is fetched via REST API from Blade JS.
     */
    public function index()
    {
        return view('earnings.index');
    }

    /**
     * GET /earnings/edit/{id}
     */
    public function edit(int $id)
    {
        return view('earnings.edit', ['earningId' => $id]);
    }
}
