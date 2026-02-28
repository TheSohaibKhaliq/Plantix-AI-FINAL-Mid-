@extends('vendor.layouts.app')
@section('title', 'Order #' . $order->id)
@section('page-title', 'Order #' . $order->id)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-receipt me-2 text-success"></i>Order Details</h4>
        <span class="text-muted small fw-medium mt-1 d-block">Manage order #{{ $order->id }} securely</span>
    </div>
    <a href="{{ route('vendor.orders.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm">
        <i class="bi bi-arrow-left me-1"></i>Back to Orders
    </a>
</div>

<div class="row g-4">
    {{-- Order Details --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-bag-check me-2 text-success fs-4"></i>Items Purchased</h5>
                <span class="badge rounded-pill shadow-sm px-3 py-2 bg-{{ match($order->order_status) {
                    'pending'=>'warning','accepted'=>'info','preparing'=>'primary',
                    'ready'=>'success','delivered'=>'success','cancelled'=>'danger',
                    default=>'secondary'} }}">
                    {{ ucfirst($order->order_status) }}
                </span>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-4">Product Name</th>
                            <th class="text-center py-3">Qty</th>
                            <th class="text-end py-3">Unit Price</th>
                            <th class="text-end py-3 px-4">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                        <tr class="hover-bg-light">
                            <td class="px-4 py-3">
                                <div class="fw-bold text-dark truncate-text" style="max-width: 300px;">{{ $item->product->name ?? $item->name }}</div>
                                @if($item->variant)<div class="small text-muted mt-1"><i class="bi bi-tags me-1"></i>{{ $item->variant }}</div>@endif
                            </td>
                            <td class="text-center py-3">
                                <span class="badge bg-light text-dark border px-2 py-1">{{ $item->quantity }}</span>
                            </td>
                            <td class="text-end py-3 fw-medium text-muted">{{ config('plantix.currency_symbol') }}{{ number_format($item->price, 2) }}</td>
                            <td class="text-end px-4 py-3 fw-bold text-dark">{{ config('plantix.currency_symbol') }}{{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light border-top-0">
                        <tr>
                            <td colspan="3" class="text-end fw-semibold py-3 text-muted">Subtotal</td>
                            <td class="text-end px-4 py-3 fw-bold text-dark">{{ config('plantix.currency_symbol') }}{{ number_format($order->sub_total ?? $order->grand_total, 2) }}</td>
                        </tr>
                        @if($order->coupon_discount)
                        <tr class="bg-success bg-opacity-10 border-top-0">
                            <td colspan="3" class="text-end py-3 fw-bold text-success"><i class="bi bi-ticket-perforated me-2"></i>Coupon Discount</td>
                            <td class="text-end px-4 py-3 fw-bold text-success">-{{ config('plantix.currency_symbol') }}{{ number_format($order->coupon_discount, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="border-top-0">
                            <td colspan="3" class="text-end py-4 fw-bold text-dark fs-5">Grand Total</td>
                            <td class="text-end px-4 py-4 fw-bold text-success fs-5">{{ config('plantix.currency_symbol') }}{{ number_format($order->grand_total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Sidebar: Customer + Update Status --}}
    <div class="col-lg-4">
        {{-- Customer Info --}}
        <div class="card border-0 shadow-sm mb-4 hover-card" style="border-radius:16px;">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-person-lines-fill me-2 text-primary fs-4"></i>Customer Info</h5>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white shadow-sm border border-3 border-white me-3"
                         style="width:50px;height:50px;font-size:1.5rem;font-weight:700">
                        {{ strtoupper(substr($order->user->name ?? 'N', 0, 1)) }}
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark">{{ $order->user->name ?? 'N/A' }}</h6>
                        <span class="small text-muted d-flex align-items-center mt-1"><i class="bi bi-envelope me-1"></i>{{ $order->user->email ?? 'No email provided' }}</span>
                    </div>
                </div>

                @if($order->user->phone)
                <div class="d-flex align-items-center mb-3 text-muted">
                    <i class="bi bi-telephone text-primary fs-5 me-3"></i>
                    <div>
                        <span class="d-block small text-uppercase fw-bold opacity-75">Phone Number</span>
                        <span class="fw-medium text-dark">{{ $order->user->phone }}</span>
                    </div>
                </div>
                @endif
                
                @if($order->delivery_address)
                <hr class="text-muted opacity-25 my-3">
                <div class="d-flex align-items-start mt-3 text-muted">
                    <i class="bi bi-geo-alt text-danger fs-5 me-3 mt-1"></i>
                    <div>
                        <span class="d-block small text-uppercase fw-bold opacity-75 mb-1">Delivery Address</span>
                        <p class="mb-0 fw-medium text-dark" style="line-height:1.5;">{{ $order->delivery_address }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Update Status --}}
        @if(!in_array($order->order_status, ['delivered','cancelled']))
        <div class="card border-0 shadow-sm mb-4 hover-card" style="border-radius:16px;">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-arrow-repeat me-2 text-warning fs-4"></i>Update Status</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('vendor.orders.status', $order->id) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted text-uppercase fw-bold small mb-2">Order Progress</label>
                        <select name="status" class="form-select form-select-lg fs-6 rounded-3 bg-light border-0 shadow-sm">
                            @php
                                $allowed = ['accepted','preparing','ready','rejected','cancelled'];
                            @endphp
                            @foreach($allowed as $s)
                                <option value="{{ $s }}" {{ $order->order_status === $s ? 'selected' : '' }}>
                                    {{ ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted text-uppercase fw-bold small mb-2">Additional Note (Optional)</label>
                        <textarea name="note" rows="3" class="form-control fs-6 rounded-3 bg-light border-0"
                                  placeholder="Message to customer regarding this update..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 rounded-pill py-3 fw-bold fs-6 shadow-sm">
                        <i class="bi bi-check-circle text-dark me-2"></i>Apply Status Update
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
