@extends('vendor.layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-4 mb-4">
    {{-- Stats Cards --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-cart-check fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Orders</div>
                    <div class="fs-4 fw-bold">{{ $stats['total_orders'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-hourglass-split fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Pending Orders</div>
                    <div class="fs-4 fw-bold">{{ $stats['pending_orders'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success">
                    <i class="bi bi-box-seam fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Products</div>
                    <div class="fs-4 fw-bold">{{ $stats['total_products'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-exclamation-circle fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Low Stock</div>
                    <div class="fs-4 fw-bold">{{ $stats['low_stock'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Revenue Cards --}}
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-muted small mb-1">Today's Revenue</div>
                <div class="fs-3 fw-bold text-success">
                    {{ config('plantix.currency_symbol') }}{{ number_format($stats['today_revenue'] ?? 0, 2) }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-muted small mb-1">This Month</div>
                <div class="fs-3 fw-bold text-primary">
                    {{ config('plantix.currency_symbol') }}{{ number_format($stats['month_revenue'] ?? 0, 2) }}
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-1"></i>Recent Orders</h6>
                <a href="{{ route('vendor.orders.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#Order</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders ?? [] as $order)
                            <tr>
                                <td><strong>#{{ $order->id }}</strong></td>
                                <td>{{ $order->user->name ?? 'N/A' }}</td>
                                <td>{{ config('plantix.currency_symbol') }}{{ number_format($order->grand_total, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ match($order->order_status) {
                                        'pending'   => 'warning',
                                        'accepted'  => 'info',
                                        'preparing' => 'primary',
                                        'ready'     => 'success',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger',
                                        default     => 'secondary'
                                    } }}">
                                        {{ ucfirst($order->order_status) }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('vendor.orders.show', $order->id) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>No orders yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
