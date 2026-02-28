<?php

namespace App\Http\Controllers;


class StoreFiltersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        return view('admin.store-filters.index');
    }


    public function edit($id)
    {
        
        return view('admin.store-filters.edit')->with('id',$id);
    }

    public function create()
    {
        return view('admin.store-filters.create');
    }    
}
