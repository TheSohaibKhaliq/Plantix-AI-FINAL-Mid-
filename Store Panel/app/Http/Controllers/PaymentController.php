<?php

namespace App\Http\Controllers;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit(int $id)
    {
        return view('payments.edit', ['paymentId' => $id]);
    }
}
