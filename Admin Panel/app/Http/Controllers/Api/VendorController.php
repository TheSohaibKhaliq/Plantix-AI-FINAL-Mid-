<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\User;
use App\Repositories\Contracts\VendorRepositoryInterface;
use App\Services\Shared\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * VendorController (API)
 *
 * All endpoints here replace the Firestore JS calls previously made
 * directly from Blade views (database.collection('vendors')...).
 */
class VendorController extends Controller
{
    public function __construct(
        private readonly VendorRepositoryInterface $vendors,
        private readonly WalletService             $wallet,
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * GET /api/vendors
     * Paginated vendor list — replaces Firestore query in stores/index.blade.php
     */
    public function index(Request $request): JsonResponse
    {
        $vendors = $this->vendors->paginated(
            $request->only(['search', 'is_approved', 'is_active', 'zone_id', 'category_id']),
            (int) $request->get('per_page', 20),
        );

        return response()->json($vendors);
    }

    /**
     * GET /api/vendors/{id}
     * Single vendor — replaces database.collection('vendors').where("id","==",id).get()
     */
    public function show(int $id): JsonResponse
    {
        $vendor = $this->vendors->findById($id);

        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found.'], 404);
        }

        return response()->json($vendor);
    }

    /**
     * PATCH /api/vendors/{id}/approve
     * Approve a vendor — replaces database.collection('vendors').doc(id).update({isApproved:true})
     */
    public function approve(int $id): JsonResponse
    {
        $this->authorize('approve', Vendor::class);

        if ($this->vendors->approve($id)) {
            return response()->json(['success' => true, 'message' => 'Vendor approved.']);
        }

        return response()->json(['success' => false], 500);
    }

    /**
     * PATCH /api/vendors/{id}/toggle-active
     * Replaces database.collection('users').doc(id).update({ active: true/false })
     */
    public function toggleActive(Request $request, int $id): JsonResponse
    {
        $this->authorize('approve', Vendor::class);

        $active  = (bool) $request->boolean('active');
        $updated = $this->vendors->toggleActive($id, $active);

        // Also update the vendor owner user's active flag
        $vendor  = Vendor::with('author')->find($id);
        if ($vendor?->author) {
            $vendor->author->update(['active' => $active]);
        }

        return response()->json(['success' => $updated]);
    }

    /**
     * DELETE /api/vendors/{id}
     * Full cascade delete: vendor + products + coupons + reviews + payouts + booked tables.
     * Replaces the multi-collection delete chain in vendors/index.blade.php.
     */
    public function destroy(int $id): JsonResponse
    {
        $vendor = Vendor::with('author')->findOrFail($id);
        $this->authorize('delete', $vendor);

        DB::transaction(function () use ($vendor) {
            // All child records have ON DELETE CASCADE foreign keys.
            // Just delete the vendor row and the DB handles the rest.
            $vendor->delete();

            // Also clean up the owning user's vendorID reference
            if ($vendor->author) {
                $vendor->author->update(['vendor_id' => null]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Vendor deleted.']);
    }

    /**
     * POST /api/vendors/{id}/wallet/credit
     * Manual wallet credit by admin — replaces database.collection('users').doc().update({wallet_amount})
     */
    public function walletCredit(Request $request, int $id): JsonResponse
    {
        $request->validate(['amount' => 'required|numeric|min:0.01']);

        $vendor = Vendor::with('author')->findOrFail($id);
        $this->authorize('approve', Vendor::class);

        $tx = $this->wallet->creditUser(
            $vendor->author,
            (float) $request->amount,
            $request->description ?? 'Manual credit by admin',
        );

        return response()->json([
            'success'        => true,
            'new_balance'    => $vendor->author->fresh()->wallet_amount,
            'transaction_id' => $tx->id,
        ]);
    }
}

