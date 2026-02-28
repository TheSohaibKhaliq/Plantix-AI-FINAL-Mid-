<?php

namespace App\Http\Controllers;

class DriverController extends Controller
{   

    public function __construct()
    {
        $this->middleware('auth');
    }
    
	  public function index()
    {
        return view("admin.drivers.index");        
    }

    public function edit($id)
    {
    	return view('admin.drivers.edit')->with('id', $id);
    }
     public function create()
    {
        return view('admin.drivers.create');
    }
    public function view($id)
    {
        return view('admin.drivers.view')->with('id', $id);
    }
    public function DocumentList($id)
    {
        return view("admin.drivers.document_list")->with('id', $id);
    }
    public function DocumentUpload($driverId, $id)
    {
        return view("admin.drivers.document_upload", compact('driverId', 'id'));
    }
}


