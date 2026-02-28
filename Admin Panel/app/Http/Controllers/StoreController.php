<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StoreController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
	  public function index()
    {

        return view("admin.stores.index");
    }

    public function vendors()
    {
        return view("admin.vendors.index");
    }

    public function edit($id)
    {
    	    return view('admin.stores.edit')->with('id',$id);
    }

    public function view($id)
    {
        return view('admin.stores.view')->with('id',$id);
    }

    public function payout($id)
    {
        return view('admin.stores.payout')->with('id',$id);
    }

    public function foods($id)
    {
        return view('admin.stores.foods')->with('id',$id);
    }

    public function orders($id)
    {
        return view('admin.stores.orders')->with('id',$id);
    }

    public function reviews($id)
    {
        return view('admin.stores.reviews')->with('id',$id);
    }

    public function promos($id)
    {
        return view('admin.stores.promos')->with('id',$id);
    }

    public function create(){
        return view('admin.stores.create');
    }

    public function DocumentList($id){
        return view("admin.vendors.document_list")->with('id',$id);
    }

    public function DocumentUpload($vendorId, $id)
    {
        return view("admin.vendors.document_upload", compact('vendorId', 'id'));
    }
}
