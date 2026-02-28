<?php

namespace App\Http\Controllers;

class CouponController extends Controller
{   

    public function __construct()
    {
        $this->middleware('auth');
    }
    
      public function index($id='')
    {
        return view("admin.coupons.index")->with('id',$id);        
    }

    public function edit($id)
    {
        return view('admin.coupons.edit')->with('id', $id);
    }

    public function create($id='')
    {
        return view('admin.coupons.create')->with('id',$id);
    }

}


