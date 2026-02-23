<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderRepository implements OrderRepositoryInterface
{
    public function paginated(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return Order::query()
            ->with(['user', 'vendor', 'driver', 'items'])
            ->when($filters['status'] ?? null, fn($q, $v) =>
                $q->where('status', $v))
            ->when($filters['payment_status'] ?? null, fn($q, $v) =>
                $q->where('payment_status', $v))
            ->when($filters['vendor_id'] ?? null, fn($q, $v) =>
                $q->where('vendor_id', $v))
            ->when($filters['user_id'] ?? null, fn($q, $v) =>
                $q->where('user_id', $v))
            ->when($filters['date_from'] ?? null, fn($q, $v) =>
                $q->whereDate('created_at', '>=', $v))
            ->when($filters['date_to'] ?? null, fn($q, $v) =>
                $q->whereDate('created_at', '<=', $v))
            ->when($filters['search'] ?? null, fn($q, $v) =>
                $q->where('order_number', 'like', "%{$v}%"))
            ->latest()
            ->paginate($perPage);
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return Order::with(['user', 'vendor', 'driver', 'items', 'coupon'])
                    ->where('order_number', $orderNumber)
                    ->first();
    }

    public function forVendor(int $vendorId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->paginated(array_merge($filters, ['vendor_id' => $vendorId]), $perPage);
    }

    public function forUser(int $userId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->paginated(['user_id' => $userId], $perPage);
    }

    public function forDriver(int $driverId, int $perPage = 20): LengthAwarePaginator
    {
        return Order::with(['vendor', 'user', 'items'])
                    ->where('driver_id', $driverId)
                    ->latest()
                    ->paginate($perPage);
    }

    public function updateStatus(int $orderId, string $status): bool
    {
        return (bool) Order::where('id', $orderId)->update(['status' => $status]);
    }

    public function revenueByPeriod(Carbon $from, Carbon $to): float
    {
        return (float) Order::where('payment_status', 'paid')
                            ->whereBetween('created_at', [$from, $to])
                            ->sum('total');
    }
}
