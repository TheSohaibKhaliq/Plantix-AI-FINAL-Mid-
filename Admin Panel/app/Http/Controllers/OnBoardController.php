<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OnBoardController extends Controller
{

    public function __construct()
    {
        // $this->middleware('auth'); // Removed to avoid guard conflicts
    }


    public function index()
    {

        return view("admin.on-board.index");
    }


    public function show($id)
    {
        return view('admin.on-board.save')->with('id', $id);
    }


}
