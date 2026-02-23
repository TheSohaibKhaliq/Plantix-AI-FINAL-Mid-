<?php

namespace App\Http\Controllers;

/**
 * WithdrawMethodController (Store Panel — Web / Blade)
 *
 * Returns Blade views only. Data is fetched/saved by the Blade JS layer
 * hitting /api/withdraw-methods (WithdrawMethodApiController) which replaced
 * the old pattern of reading/writing stripe_account_id, paypal_email, etc.
 * directly on the vendor Firestore document.
 */
class WithdrawMethodController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('withdraw_method.index');
    }

    public function create()
    {
        return view('withdraw_method.create');
    }
}