<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth'); // Removed to avoid guard conflicts
    }

    public function index($type)
    {
        if ($type == "sales") {
            return view('admin.reports.sales-report');
        }
    }
}

?>