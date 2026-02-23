<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Vendor;

interface VendorRepositoryInterface
{
    public function paginated(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function findById(int $id): ?Vendor;

    public function findByAuthorId(int $userId): ?Vendor;

    public function approve(int $vendorId): bool;

    public function toggleActive(int $vendorId, bool $active): bool;

    public function delete(int $vendorId): bool;

    public function pendingApprovals(int $perPage = 20): LengthAwarePaginator;
}
