<?php

namespace App\Http\Controllers;


class CmsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
         return view('admin.cms.index');
    }
    public function Edit($id)
    {
        return view('admin.cms.edit')->with('id',$id);
    }

    public function Create()
    {
        return view('admin.cms.create');
    }

}