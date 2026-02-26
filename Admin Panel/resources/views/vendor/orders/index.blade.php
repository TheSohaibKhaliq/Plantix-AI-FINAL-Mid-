@extends('vendor.layouts.app')
@section('title', 'Orders')
@section('page-title', 'Orders')

@section('content')
{{-- Filter Bar --}}
<form method="GET" class="card border-0 shadow-sm mb-4">
    <div class="card-body py-2">
        <div class="row g-2 align-items-center">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    @foreach(['pending','accepted','preparing','ready','delivered','cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
                <a href="{{ route('vendor.orders.index') }}" class="btn btn-outline-secondary ms-1">Reset</a>
            </div>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#Order</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td><strong>#{{ $order->id }}</strong></td>
                        <td>{{ $order->user->name ?? 'N/A' }}</td>
                        <td>{{ $order->order_items_count ?? $order->orderItems->count() }}</td>
                        <td>{{ config('plantix.currency_symbol') }}{{ number_format($order->grand_total, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                                {{ ucfirst($order->payment_status ?? 'pending') }}
                            </span>
                        </td>
                        <td>
                            @php
                                $colors = ['pending'=>'warning','accepted'=>'info','preparing'=>'primary',
                                           'ready'=>'success','delivered'=>'success','cancelled'=>'danger'];
                                $c = $colors[$order->order_status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $c }}">{{ ucfirst($order->order_status) }}</span>
                        </td>
                        <td class="text-muted small">{{ $order->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('vendor.orders.show', $order->id) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-cart-x fs-2 d-block mb-2"></i>No orders found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($orders->hasPages())
    <div class="card-footer bg-transparent">
        {{ $orders->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
