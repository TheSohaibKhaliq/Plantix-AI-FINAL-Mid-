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

        return view("stores.index");
    }

    public function vendors()
    {
        return view("vendors.index");
    }

    public function edit($id)
    {
    	    return view('stores.edit')->with('id',$id);
    }

    public function view($id)
    {
        return view('stores.view')->with('id',$id);
    }

    public function payout($id)
    {
        return view('stores.payout')->with('id',$id);
    }

    public function foods($id)
    {
        return view('stores.foods')->with('id',$id);
    }

    public function orders($id)
    {
        return view('stores.orders')->with('id',$id);
    }

    public function reviews($id)
    {
        return view('stores.reviews')->with('id',$id);
    }

    public function promos($id)
    {
        return view('stores.promos')->with('id',$id);
    }

    public function create(){
        return view('stores.create');
    }

    public function DocumentList($id){
        return view("vendors.document_list")->with('id',$id);
    }

    public function DocumentUpload($vendorId, $id)
    {
        return view("vendors.document_upload", compact('vendorId', 'id'));
    }
}
