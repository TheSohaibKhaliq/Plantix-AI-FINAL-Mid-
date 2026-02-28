<?php

namespace App\Http\Controllers;


class MapController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        return view('admin.map.index');
    }

}