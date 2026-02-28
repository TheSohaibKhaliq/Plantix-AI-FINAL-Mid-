<?php

namespace App\Http\Controllers;

class AttributeController extends Controller
{   

    public function __construct()
    {
        // $this->middleware('auth'); // Removed to avoid guard conflicts
    }
    
	  public function index()
    {
        return view("admin.attributes.index");
    }

     public function edit($id)
    {
    	return view('admin.attributes.edit')->with('id', $id);
    }

    public function create()
    {
        return view('admin.attributes.create');
    }

}


