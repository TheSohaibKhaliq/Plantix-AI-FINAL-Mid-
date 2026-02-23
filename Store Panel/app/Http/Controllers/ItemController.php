<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * ItemController (Store Panel — Web / Blade)
 *
 * Returns Blade views only. Data is fetched/saved by the Blade JS layer
 * hitting /api/products (ProductController) which replaced all
 * database.collection('vendor_products')... Firestore calls.
 */
class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('foods.index');
    }

    public function edit(int $id)
    {
        // Pass the MySQL product ID to the view; JS fetches full data from /api/products/{id}
        return view('foods.edit', ['productId' => $id]);
    }

    public function create()
    {
        // Pass category list for the form dropdown — avoids a separate Firestore call
        $categories = Category::where('active', true)->orderBy('name')->get(['id', 'name']);
        return view('foods.create', compact('categories'));
    }
}

