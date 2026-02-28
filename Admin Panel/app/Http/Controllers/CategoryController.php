<?php

namespace App\Http\Controllers;

class CategoryController extends Controller
{   

    public function __construct()
    {
        // $this->middleware('auth'); // Removed to avoid guard conflicts
    }
    
	  public function index()
    {
        return view("admin.categories.index");
        
    }

     public function edit($id)
    {
    	return view('admin.categories.edit')->with('id', $id);
    }

    public function create()
    {
        return view('admin.categories.create');
    }

}


