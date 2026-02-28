<?php

namespace App\Http\Controllers;


class DriversPayoutController extends Controller
{

   public function __construct()
    {
        // $this->middleware('auth'); // Removed to avoid guard conflicts
    }
    
    public function index($id='')
    {

       return view("admin.drivers-payouts.index")->with('id',$id);
    }

    public function create($id='')
    {
        
       return view("admin.drivers-payouts.create")->with('id',$id);
    }

}
