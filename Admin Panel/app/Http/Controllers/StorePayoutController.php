<?php

namespace App\Http\Controllers;


class StorePayoutController extends Controller
{  

   public function __construct()
    {
        // $this->middleware('auth'); // Removed to avoid guard conflicts
    }

    public function index($id='')
    {

       return view("admin.store-payouts.index")->with('id',$id);
    }

    public function create($id='')
    {
        
       return view("admin.store-payouts.create")->with('id',$id);
    }

}
