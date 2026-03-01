<?php

namespace App\Services\Vendor;

use App\Models\Coupon;
use App\Models\Vendor;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Vendor Coupon Service
 * Section 4 – Vendor Flow: Discount / Coupon Creation
 *
 * Manages vendor-scoped coupons with all validation rules from Section 11.
 */
class VendorCouponService
{
    /**
     * Return paginated coupons scoped to this vendor.
     */
    public function getCoupons(Vendor $vendor, int $perPage = 20): LengthAwarePaginator
    {
        return Coupon::where('vendor_id', $vendor->id)
            ->withCount('usages')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new vendor coupon.
     *
     * @param  Vendor  $vendor
     * @param  array   $data  Validated fields from the request
     * @return Coupon
     * @throws ValidationException
     */
    public function create(Vendor $vendor, array $data): Coupon
    {
        // Auto-generate code if not provided
        $code = ! empty($data['code'])
            ? strtoupper(trim($data['code']))
            : strtoupper('PLX-' . Str::random(6));

        $this->assertCodeUnique($code);

        return Coupon::create([
            'vendor_id'              => $vendor->id,
            'code'                   => $code,
            'type'                   => $data['type'],           // percentage | fixed
            'value'                  => $data['value'],
            'min_order'              => $data['min_order'] ?? null,
            'max_discount'           => $data['max_discount'] ?? null,
            'usage_limit'            => $data['usage_limit'] ?? null,
            'usage_limit_per_user'   => $data['usage_limit_per_user'] ?? null,
            'starts_at'              => $data['starts_at'] ?? now(),
            'expires_at'             => $data['expires_at'] ?? null,
            'is_active'              => (bool) ($data['is_active'] ?? true),
            'used_count'             => 0,
        ]);
    }

    /**
     * Update an existing vendor coupon.
     * Ownership is verified before update.
     */
    public function update(Vendor $vendor, int $couponId, array $data): Coupon
    {
        $coupon = $this->scopedFind($vendor, $couponId);

        if (! empty($data['code'])) {
            $newCode = strtoupper(trim($data['code']));
            if ($newCode !== $coupon->code) {
                $this->assertCodeUnique($newCode, $couponId);
                $data['code'] = $newCode;
            }
        }

        $coupon->update(array_filter([
            'code'                   => $data['code'] ?? null,
            'type'                   => $data['type'] ?? null,
            'value'                  => $data['value'] ?? null,
            'min_order'              => $data['min_order'] ?? null,
            'max_discount'           => $data['max_discount'] ?? null,
            'usage_limit'            => $data['usage_limit'] ?? null,
            'usage_limit_per_user'   => $data['usage_limit_per_user'] ?? null,
            'starts_at'              => $data['starts_at'] ?? null,
            'expires_at'             => $data['expires_at'] ?? null,
            'is_active'              => isset($data['is_active']) ? (bool) $data['is_active'] : null,
        ], fn ($v) => $v !== null));

        return $coupon->fresh();
    }

    /**
     * Toggle is_active on a coupon.
     */
    public function toggle(Vendor $vendor, int $couponId): Coupon
    {
        $coupon = $this->scopedFind($vendor, $couponId);
        $coupon->update(['is_active' => ! $coupon->is_active]);

        return $coupon->fresh();
    }

    /**
     * Delete a coupon (only if it has never been used).
     */
    public function delete(Vendor $vendor, int $couponId): void
    {
        $coupon = $this->scopedFind($vendor, $couponId);

        if ($coupon->used_count > 0) {
            throw ValidationException::withMessages([
                'coupon' => 'Cannot delete a coupon that has already been used. Deactivate it instead.',
            ]);
        }

        $coupon->delete();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function scopedFind(Vendor $vendor, int $couponId): Coupon
    {
        return Coupon::where('vendor_id', $vendor->id)->findOrFail($couponId);
    }

    /**
     * Verify code uniqueness across ALL vendors (as per Section 4 blueprint).
     */
    private function assertCodeUnique(string $code, ?int $excludeId = null): void
    {
        $exists = Coupon::where('code', $code)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'code' => "Coupon code \"{$code}\" is already in use by another vendor.",
            ]);
        }
    }
}
