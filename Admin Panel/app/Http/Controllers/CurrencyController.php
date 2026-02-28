<?php


namespace App\Http\Controllers;

class CurrencyController extends Controller
{ 


    public function __construct()
    {
        $this->middleware('auth');
    }
    
	    public function index()
    {
       return view("admin.settings.currencies.index");
    }


  public function edit($id)
    {
    	return view('admin.settings.currencies.edit')->with('id',$id);
    }
    public function create(){
       return view('admin.settings.currencies.create');

    }

}