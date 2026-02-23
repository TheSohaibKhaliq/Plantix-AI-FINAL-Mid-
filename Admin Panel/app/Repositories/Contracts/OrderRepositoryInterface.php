<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Order;

interface OrderRepositoryInterface
{
    public function paginated(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function findByOrderNumber(string $orderNumber): ?Order;

    public function forVendor(int $vendorId, array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function forUser(int $userId, int $perPage = 20): LengthAwarePaginator;

    public function forDriver(int $driverId, int $perPage = 20): LengthAwarePaginator;

    public function updateStatus(int $orderId, string $status): bool;

    public function revenueByPeriod(\Carbon\Carbon $from, \Carbon\Carbon $to): float;
}
