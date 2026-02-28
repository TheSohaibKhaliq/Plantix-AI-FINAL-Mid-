<?php

namespace App\Http\Controllers;


class AdminPaymentsController extends Controller
{  

   public function __construct()
    {
        // $this->middleware('auth'); // Removed to avoid guard conflicts
    }
    
	public function index()
    {
       return view("admin.payments.index");
    }

    public function driverIndex()
 	{
    	return view("admin.payments.driver_index");
 	}

}
