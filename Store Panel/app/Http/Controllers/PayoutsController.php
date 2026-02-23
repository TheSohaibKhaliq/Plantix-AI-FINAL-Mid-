<?php

namespace App\Http\Controllers;

/**
 * PayoutsController (Store Panel — Web / Blade)
 *
 * Returns Blade views only. Data is fetched via /api/payouts
 * (StorePayoutController) which replaced Firestore payout queries.
 */
class PayoutsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('restaurants_payouts.index');
    }

    public function create()
    {
        return view('restaurants_payouts.create');
    }
}