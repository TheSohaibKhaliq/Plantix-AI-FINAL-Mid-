<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['vendor', 'category', 'primaryImage', 'approvedReviews'])
            ->active()
            ->inStock();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('brand')) {
            $query->whereHas('brand', fn($q) => $q->where('slug', $request->brand));
        }

        if ($request->filled('vendor')) {
            $query->whereHas('vendor', fn($q) => $q->where('slug', $request->vendor));
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $sort = match ($request->sort) {
            'price_asc'  => ['price', 'asc'],
            'price_desc' => ['price', 'desc'],
            'newest'     => ['created_at', 'desc'],
            'popular'    => ['sort_order', 'asc'],
            default      => ['created_at', 'desc'],
        };

        $products   = $query->orderBy(...$sort)->paginate(16)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $brands     = Brand::active()->orderBy('name')->get();

        return view('pages.shop', compact('products', 'categories', 'brands'));
    }

    public function show(int $id): View
    {
        $product  = Product::with([
            'vendor', 'category', 'brand', 'images',
            'attributes', 'approvedReviews.user', 'stock',
        ])->active()->findOrFail($id);

        $related = Product::active()
                          ->inStock()
                          ->where('category_id', $product->category_id)
                          ->where('id', '!=', $product->id)
                          ->limit(4)
                          ->get();

        return view('pages.shop-single', compact('product', 'related'));
    }
}
