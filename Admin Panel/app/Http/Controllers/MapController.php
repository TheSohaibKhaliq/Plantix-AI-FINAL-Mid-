<?php

namespace App\Http\Controllers;


class MapController extends Controller
{

    public function __construct()
    {
        // $this->middleware('auth'); // Removed to avoid guard conflicts
    }

    public function index()
    {

        return view('admin.map.index');
    }

}