<?php

namespace App\Http\Controllers;

class ReviewAttributeController extends Controller
{   

    public function __construct()
    {
        $this->middleware('auth');
    }
    
	  public function index()
    {
        return view("admin.review-attributes.index");
    }

     public function edit($id)
    {
    	return view('admin.review-attributes.edit')->with('id', $id);
    }

    public function create()
    {
        return view('admin.review-attributes.create');
    }

}


