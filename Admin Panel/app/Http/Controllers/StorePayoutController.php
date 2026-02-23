<?php

namespace App\Http\Controllers;


class StorePayoutController extends Controller
{  

   public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id='')
    {

       return view("store_payouts.index")->with('id',$id);
    }

    public function create($id='')
    {
        
       return view("store_payouts.create")->with('id',$id);
    }

}
