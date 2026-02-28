@extends('layouts.app')

@section('title', 'Orders')

@section('content')
<div class="container-fluid">

    <div class="row page-titles border-bottom pb-3 mb-4">
        <div class="col-md-12 align-self-center">
            <h3 class="text-themecolor fw-bold mb-0"><i class="bi bi-bag-check me-2 text-success"></i>Orders</h3>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
        <div class="card-body p-4">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Order # or customer…" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $s)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Vendor ID</label>
                    <input type="number" name="vendor_id" class="form-control form-control-sm"
                           placeholder="Vendor ID" value="{{ request('vendor_id') }}">
                </div>
                <div class="col-auto d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-success fw-bold rounded shadow-sm">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-light border fw-bold text-muted rounded shadow-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
            <span class="fw-bold text-success"><i class="fa fa-list me-2"></i>{{ $orders->total() }} order(s)</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Vendor</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Placed</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td class="fw-bold fs-6">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-success text-decoration-none">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td>{{ $order->user->name ?? 'Deleted User' }}</td>
                            <td>{{ $order->vendor->name ?? '—' }}</td>
                            <td>{{ $order->items->count() }}</td>
                            <td>{{ config('plantix.currency_symbol', 'Rs') }} {{ number_format($order->total, 2) }}</td>
                            <td>
                                @php
                                    $payClass = ['paid'=>'success','pending'=>'warning','failed'=>'danger','refunded'=>'secondary'];
                                @endphp
                                <span class="badge bg-{{ $payClass[$order->payment_status] ?? 'secondary' }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $bc = [
                                        'pending'         => 'warning',
                                        'accepted'        => 'info',
                                        'preparing'       => 'primary',
                                        'ready'           => 'secondary',
                                        'driver_assigned' => 'dark',
                                        'picked_up'       => 'light',
                                        'delivered'       => 'success',
                                        'rejected'        => 'danger',
                                        'cancelled'       => 'danger',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $bc[$order->status] ?? 'secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                            <td class="text-muted small">{{ $order->created_at->format('d M Y H:i') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                   class="btn btn-sm btn-info rounded-pill text-white shadow-sm px-3 fw-bold" title="View">
                                    <i class="bi bi-eye me-1"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                No orders found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($orders->hasPages())
        <div class="card-footer bg-white py-3 border-top" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
