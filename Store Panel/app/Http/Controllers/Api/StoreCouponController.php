<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * StoreCouponController (Store Panel API)
 *
 * Replaces Firestore coupons collection JS calls in:
 *   - coupons/index.blade.php
 *   - coupons/create.blade.php
 *   - coupons/edit.blade.php
 */
class StoreCouponController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    private function vendorId(): int
    {
        return Auth::user()->vendor_id
            ?? abort(403, 'No vendor account linked.');
    }

    /**
     * GET /api/coupons
     * Replaces: database.collection('coupons').where('vendor_id','==',id).get()
     */
    public function index(Request $request): JsonResponse
    {
        $coupons = Coupon::where('vendor_id', $this->vendorId())
            ->when($request->active, fn($q) => $q->where('active', true))
            ->skip((int) $request->get('skip', 0))
            ->take((int) $request->get('limit', 50))
            ->get();

        return response()->json($coupons);
    }

    /**
     * POST /api/coupons
     * Replaces: database.collection('coupons').add({...})
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code'             => 'required|string|max:50|unique:coupons',
            'discount_type'    => 'required|in:percentage,fixed',
            'discount_value'   => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_uses'         => 'nullable|integer|min:1',
            'max_uses_per_user'=> 'nullable|integer|min:1',
            'starts_at'        => 'nullable|date',
            'expires_at'       => 'nullable|date|after:starts_at',
            'active'           => 'boolean',
        ]);

        $validated['vendor_id'] = $this->vendorId();
        $coupon = Coupon::create($validated);

        return response()->json($coupon, 201);
    }

    /**
     * GET /api/coupons/{coupon}
     */
    public function show(Coupon $coupon): JsonResponse
    {
        $this->authorizeCoupon($coupon);
        return response()->json($coupon);
    }

    /**
     * PUT /api/coupons/{coupon}
     * Replaces: database.collection('coupons').doc(id).update({...})
     */
    public function update(Request $request, Coupon $coupon): JsonResponse
    {
        $this->authorizeCoupon($coupon);

        $validated = $request->validate([
            'code'             => 'sometimes|string|max:50|unique:coupons,code,' . $coupon->id,
            'discount_type'    => 'sometimes|in:percentage,fixed',
            'discount_value'   => 'sometimes|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_uses'         => 'nullable|integer|min:1',
            'max_uses_per_user'=> 'nullable|integer|min:1',
            'starts_at'        => 'nullable|date',
            'expires_at'       => 'nullable|date',
            'active'           => 'boolean',
        ]);

        $coupon->update($validated);
        return response()->json($coupon);
    }

    /**
     * DELETE /api/coupons/{coupon}
     * Replaces: database.collection('coupons').doc(id).delete()
     */
    public function destroy(Coupon $coupon): JsonResponse
    {
        $this->authorizeCoupon($coupon);
        $coupon->delete();
        return response()->json(null, 204);
    }

    private function authorizeCoupon(Coupon $coupon): void
    {
        if ($coupon->vendor_id !== $this->vendorId() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized.');
        }
    }
}
