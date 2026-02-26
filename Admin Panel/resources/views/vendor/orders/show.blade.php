@extends('vendor.layouts.app')
@section('title', 'Order #' . $order->id)
@section('page-title', 'Order #' . $order->id)

@section('content')
<div class="row g-4">
    {{-- Order Details --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between">
                <h6 class="mb-0 fw-semibold">Order Items</h6>
                <span class="badge bg-{{ match($order->order_status) {
                    'pending'=>'warning','accepted'=>'info','preparing'=>'primary',
                    'ready'=>'success','delivered'=>'success','cancelled'=>'danger',
                    default=>'secondary'} }}">
                    {{ ucfirst($order->order_status) }}
                </span>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $item->product->name ?? $item->name }}</div>
                                @if($item->variant)<small class="text-muted">{{ $item->variant }}</small>@endif
                            </td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">{{ config('plantix.currency_symbol') }}{{ number_format($item->price, 2) }}</td>
                            <td class="text-end">{{ config('plantix.currency_symbol') }}{{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end fw-semibold">Subtotal</td>
                            <td class="text-end">{{ config('plantix.currency_symbol') }}{{ number_format($order->sub_total ?? $order->grand_total, 2) }}</td>
                        </tr>
                        @if($order->coupon_discount)
                        <tr class="text-success">
                            <td colspan="3" class="text-end">Coupon Discount</td>
                            <td class="text-end">-{{ config('plantix.currency_symbol') }}{{ number_format($order->coupon_discount, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Grand Total</td>
                            <td class="text-end fw-bold">{{ config('plantix.currency_symbol') }}{{ number_format($order->grand_total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Sidebar: Customer + Update Status --}}
    <div class="col-lg-4">
        {{-- Customer Info --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent"><h6 class="mb-0">Customer</h6></div>
            <div class="card-body">
                <p class="mb-1"><strong>{{ $order->user->name ?? 'N/A' }}</strong></p>
                <p class="mb-1 text-muted small">{{ $order->user->email ?? '' }}</p>
                <p class="mb-1 text-muted small">{{ $order->user->phone ?? '' }}</p>
                @if($order->delivery_address)
                    <hr class="my-2">
                    <p class="mb-0 small text-muted">{{ $order->delivery_address }}</p>
                @endif
            </div>
        </div>

        {{-- Update Status --}}
        @if(!in_array($order->order_status, ['delivered','cancelled']))
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent"><h6 class="mb-0">Update Status</h6></div>
            <div class="card-body">
                <form method="POST" action="{{ route('vendor.orders.status', $order->id) }}">
                    @csrf
                    <select name="status" class="form-select mb-3">
                        @php
                            $allowed = ['accepted','preparing','ready','rejected','cancelled'];
                        @endphp
                        @foreach($allowed as $s)
                            <option value="{{ $s }}" {{ $order->order_status === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                    <textarea name="note" rows="2" class="form-control mb-3"
                              placeholder="Optional note…"></textarea>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-lg me-1"></i>Update
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('vendor.orders.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Orders
    </a>
</div>
@endsection
