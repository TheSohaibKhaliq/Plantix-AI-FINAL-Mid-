<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    
    public function __construct()
    {
       // $this->middleware('auth'); // Removed to avoid guard conflicts
    }

 
    
    public function index($id='')
    {
        return view("admin.transactions.index")->with('id',$id);
    }
}
