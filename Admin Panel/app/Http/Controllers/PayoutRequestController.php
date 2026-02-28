<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PayoutRequestController extends Controller
{

    public function __construct()
    {
        // $this->middleware('auth'); // Removed to avoid guard conflicts
    }

    public function index($id = '')
    {
        return view("admin.payout-requests.admin.drivers.index")->with('id',$id);
    }

    public function store($id = '')
    {
        return view("admin.payout-requests.stores.index")->with('id',$id);
        
    }

}
