<?php

namespace App\Http\Controllers;

class GiftCardController extends Controller
{

    public function __construct()
    {
        // $this->middleware('auth'); // Removed to avoid guard conflicts
    }
    public function index()
    {
        return view("admin.gift-cards.index");
        
    }

    public function save($id="")
    {
        return view('admin.gift-cards.save')->with('id', $id);
    }
    public function edit($id)
    {
        return view('admin.gift-cards.save')->with('id', $id);
    }

}
