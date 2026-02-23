<?php

namespace App\Http\Controllers;

/**
 * CouponController (Store Panel — Web / Blade)
 *
 * Returns Blade views only. Data is fetched/saved by Blade JS hitting
 * /api/coupons (StoreCouponController) which replaced Firestore coupon calls.
 */
class CouponController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('coupons.index');
    }

    public function edit(int $id)
    {
        return view('coupons.edit', ['couponId' => $id]);
    }

    public function create()
    {
        return view('coupons.create');
    }
}


