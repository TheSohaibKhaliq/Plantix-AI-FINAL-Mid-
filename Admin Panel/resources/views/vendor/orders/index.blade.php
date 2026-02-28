@extends('vendor.layouts.app')
@section('title', 'Orders')
@section('page-title', 'Orders')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-cart-check me-2 text-success"></i>Recent Orders</h4>
        <span class="text-muted small fw-medium mt-1 d-block">Manage and track customer orders</span>
    </div>
</div>

{{-- Filter Bar --}}
<form method="GET" class="card border-0 shadow-sm mb-4 hover-card" style="border-radius:16px;">
    <div class="card-body p-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-3">
                <label class="form-label small text-uppercase fw-bold text-muted mb-1">Order Status</label>
                <select name="status" class="form-select border-0 bg-light shadow-sm">
                    <option value="">All Statuses</option>
                    @foreach(['pending','accepted','preparing','ready','delivered','cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-uppercase fw-bold text-muted mb-1">Date Filter</label>
                <input type="date" name="date" class="form-control border-0 bg-light shadow-sm" value="{{ request('date') }}">
            </div>
            <div class="col-auto ms-auto align-self-end">
                <button type="submit" class="btn btn-success px-4 rounded-pill fw-bold shadow-sm">
                    <i class="bi bi-funnel me-1"></i>Apply Filters
                </button>
                <a href="{{ route('vendor.orders.index') }}" class="btn btn-outline-secondary ms-2 px-4 rounded-pill">Reset</a>
            </div>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3 px-4 rounded-top-left-3">Order ID</th>
                        <th class="py-3">Customer</th>
                        <th class="py-3 text-center">Items</th>
                        <th class="py-3">Total Amount</th>
                        <th class="py-3">Payment</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Date Ordered</th>
                        <th class="text-end py-3 px-4 rounded-top-right-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr class="hover-bg-light">
                        <td class="px-4">
                            <span class="fw-bold text-dark fs-6">#{{ $order->id }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $order->user->name ?? 'N/A' }}</div>
                            <div class="small text-muted">{{ $order->user->phone ?? '' }}</div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border px-2 py-1">{{ $order->order_items_count ?? $order->orderItems->count() }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-success fs-6">{{ config('plantix.currency_symbol') }}{{ number_format($order->grand_total, 2) }}</div>
                        </td>
                        <td>
                            <span class="badge rounded-pill px-3 py-2 bg-opacity-10 shadow-sm border
                                bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}
                                text-{{ $order->payment_status === 'paid' ? 'success' : 'warning text-dark' }}
                                border-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                                <i class="bi bi-{{ $order->payment_status === 'paid' ? 'check-circle' : 'hourglass-split' }} me-1"></i>
                                {{ ucfirst($order->payment_status ?? 'pending') }}
                            </span>
                        </td>
                        <td>
                            @php
                                $colors = ['pending'=>'warning','accepted'=>'info','preparing'=>'primary',
                                           'ready'=>'success','delivered'=>'success','cancelled'=>'danger'];
                                $c = $colors[$order->order_status] ?? 'secondary';
                            @endphp
                            <span class="badge rounded-pill bg-{{ $c }} shadow-sm px-3 py-2 border border-{{ $c }}">
                                {{ ucfirst($order->order_status) }}
                            </span>
                        </td>
                        <td>
                            <div class="text-dark fw-medium small"><i class="bi bi-calendar3 text-muted me-1"></i>{{ $order->created_at->format('d M Y, h:i A') }}</div>
                        </td>
                        <td class="text-end px-4">
                            <a href="{{ route('vendor.orders.show', $order->id) }}"
                               class="btn btn-sm btn-outline-success rounded-pill px-3 shadow-sm py-2" title="View Order Details">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width:100px; height:100px;">
                                <i class="bi bi-cart-x fs-1 text-success opacity-50"></i>
                            </div>
                            <h4 class="fw-bold text-dark">No Orders Found</h4>
                            <p class="text-muted">You do not have any orders matching these criteria right now.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($orders->hasPages())
    <div class="card-footer bg-white p-4" style="border-bottom-left-radius:16px; border-bottom-right-radius:16px;">
        {{ $orders->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
