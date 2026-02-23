<?php

namespace App\Repositories\Eloquent;

use App\Models\Vendor;
use App\Repositories\Contracts\VendorRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class VendorRepository implements VendorRepositoryInterface
{
    public function paginated(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return Vendor::query()
            ->with(['author', 'category', 'zone'])
            ->when($filters['search'] ?? null, fn($q, $v) =>
                $q->whereFullText(['title', 'description'], $v)
                  ->orWhere('title', 'like', "%{$v}%"))
            ->when(isset($filters['is_approved']), fn($q) =>
                $q->where('is_approved', (bool) $filters['is_approved']))
            ->when(isset($filters['is_active']), fn($q) =>
                $q->where('is_active', (bool) $filters['is_active']))
            ->when($filters['zone_id'] ?? null, fn($q, $v) =>
                $q->where('zone_id', $v))
            ->when($filters['category_id'] ?? null, fn($q, $v) =>
                $q->where('category_id', $v))
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): ?Vendor
    {
        return Vendor::with(['author', 'category', 'zone', 'products', 'storeFilters'])
                     ->find($id);
    }

    public function findByAuthorId(int $userId): ?Vendor
    {
        return Vendor::where('author_id', $userId)->first();
    }

    public function approve(int $vendorId): bool
    {
        $updated = Vendor::where('id', $vendorId)
                         ->update(['is_approved' => true, 'is_active' => true]);
        Cache::tags(['vendors'])->flush();
        return (bool) $updated;
    }

    public function toggleActive(int $vendorId, bool $active): bool
    {
        $updated = Vendor::where('id', $vendorId)->update(['is_active' => $active]);
        Cache::tags(['vendors'])->flush();
        return (bool) $updated;
    }

    public function delete(int $vendorId): bool
    {
        // Cascading deletes are handled by FK constraints in the migration.
        $vendor = Vendor::findOrFail($vendorId);
        $deleted = $vendor->delete();
        Cache::tags(['vendors'])->flush();
        return (bool) $deleted;
    }

    public function pendingApprovals(int $perPage = 20): LengthAwarePaginator
    {
        return Vendor::with(['author', 'category'])
                     ->where('is_approved', false)
                     ->latest()
                     ->paginate($perPage);
    }
}
