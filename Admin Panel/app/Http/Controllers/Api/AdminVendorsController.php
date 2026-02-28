<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;

class AdminVendorsController extends Controller
{
    /**
     * Get top vendors
     */
    public function top(Request $request)
    {
        try {
            $limit = $request->get('limit', 5);
            $vendors = Vendor::orderBy('reviews_count', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($vendor) {
                    return [
                        'id' => $vendor->id,
                        'title' => $vendor->title ?? $vendor->name,
                        'photo' => $vendor->photo ?? null,
                        'reviews_count' => $vendor->reviews_count ?? 0,
                        'reviews_sum' => $vendor->reviews_sum ?? 0,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $vendors
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching vendors: ' . $e->getMessage()
            ], 500);
        }
    }
}
