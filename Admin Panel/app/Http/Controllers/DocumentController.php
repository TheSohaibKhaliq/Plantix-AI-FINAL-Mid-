<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentController extends Controller
{

    public function __construct()
    {
        // $this->middleware('auth'); // Removed to avoid guard conflicts
    }

    public function index()
    {
        return view('admin.documents.index');
    }
    public function create()
    {
        return view("admin.documents.create");
    }
    public function edit($id)
    {
        return view("admin.documents.edit")->with('id',$id);
    }
}
