<?php

namespace App\Http\Controllers;

class ZoneController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.zones.index');
    }
    public function edit($id)
    {
        return view('admin.zones.edit')->with('id',$id);
    }

    public function create()
    {
        return view('admin.zones.create');
    }
}