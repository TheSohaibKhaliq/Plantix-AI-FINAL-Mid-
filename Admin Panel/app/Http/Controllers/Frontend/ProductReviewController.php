<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductReviewController extends Controller
{
    public function store(Request $request, int $productId): RedirectResponse
    {
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        $product = Product::findOrFail($productId);
        $user    = auth('web')->user();

        // One review per product per user
        $existing = Review::where('product_id', $product->id)
                          ->where('user_id', $user->id)
                          ->first();

        if ($existing) {
            $existing->update([
                'rating'  => $request->rating,
                'comment' => $request->comment,
                'status'  => 'pending', // re-submit for approval
            ]);
        } else {
            Review::create([
                'product_id' => $product->id,
                'user_id'    => $user->id,
                'vendor_id'  => $product->vendor_id,
                'rating'     => $request->rating,
                'comment'    => $request->comment,
                'status'     => 'pending',
            ]);
        }

        return back()->with('success', 'Review submitted. It will appear after admin approval.');
    }

    public function destroy(int $reviewId): RedirectResponse
    {
        $user   = auth('web')->user();
        $review = Review::where('user_id', $user->id)->findOrFail($reviewId);
        $review->delete();

        return back()->with('success', 'Review deleted.');
    }
}
