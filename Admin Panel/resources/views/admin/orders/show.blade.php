@extends('layouts.app')

@section('title', 'Order ' . $order->order_number)

@section('content')
<div class="container-fluid">

    <div class="row page-titles border-bottom pb-3 mb-4">
        <div class="col-md-5 align-self-center d-flex align-items-center">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-light border shadow-sm rounded-circle d-flex align-items-center justify-content-center me-3" style="width:40px;height:40px;">
                <i class="fas fa-arrow-left text-muted"></i>
            </a>
            <h3 class="text-themecolor fw-bold mb-0">Order {{ $order->order_number }}</h3>
        </div>
        <div class="col-md-7 align-self-center d-flex justify-content-end align-items-center">
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
            <span class="badge fw-bold shadow-sm px-3 py-2 fs-6 bg-{{ $bc[$order->status] ?? 'secondary' }}">
                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- Left column: items + status history --}}
        <div class="col-lg-8">

            {{-- Order Items --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
                <div class="card-header bg-white border-bottom py-3 fw-bold text-success">
                    <i class="bi bi-cart3 me-2"></i>Order Items ({{ $order->items->count() }})
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        @if($item->product)
                                            <a href="{{ route('admin.products.show', $item->product->id) }}"
                                               class="text-decoration-none">
                                                {{ $item->product->name }}
                                            </a>
                                        @else
                                            {{ $item->product_name ?? 'Deleted Product' }}
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">
                                        {{ config('plantix.currency_symbol', 'Rs') }} {{ number_format($item->unit_price ?? $item->price ?? 0, 2) }}
                                    </td>
                                    <td class="text-end">
                                        {{ config('plantix.currency_symbol', 'Rs') }} {{ number_format(($item->unit_price ?? $item->price ?? 0) * $item->quantity, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light fw-semibold">
                                <tr>
                                    <td colspan="3" class="text-end">Subtotal</td>
                                    <td class="text-end">{{ config('plantix.currency_symbol', 'Rs') }} {{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end">Delivery Fee</td>
                                    <td class="text-end">{{ config('plantix.currency_symbol', 'Rs') }} {{ number_format($order->delivery_fee, 2) }}</td>
                                </tr>
                                @if($order->discount_amount > 0)
                                <tr class="text-success">
                                    <td colspan="3" class="text-end">Discount</td>
                                    <td class="text-end">− {{ config('plantix.currency_symbol', 'Rs') }} {{ number_format($order->discount_amount, 2) }}</td>
                                </tr>
                                @endif
                                @if($order->tax_amount > 0)
                                <tr>
                                    <td colspan="3" class="text-end">Tax</td>
                                    <td class="text-end">{{ config('plantix.currency_symbol', 'Rs') }} {{ number_format($order->tax_amount, 2) }}</td>
                                </tr>
                                @endif
                                <tr class="fs-5">
                                    <td colspan="3" class="text-end">Total</td>
                                    <td class="text-end fw-bold">{{ config('plantix.currency_symbol', 'Rs') }} {{ number_format($order->total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Status History --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
                <div class="card-header bg-white border-bottom py-3 fw-bold text-success">
                    <i class="bi bi-clock-history me-2"></i>Status History
                </div>
                <ul class="list-group list-group-flush">
                    @forelse($order->statusHistory as $h)
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div>
                            <span class="badge bg-{{ $bc[$h->status] ?? 'secondary' }} me-2">
                                {{ ucfirst(str_replace('_', ' ', $h->status)) }}
                            </span>
                            @if($h->notes)
                                <small class="text-muted">{{ $h->notes }}</small>
                            @endif
                        </div>
                        <small class="text-muted text-nowrap ms-3">
                            {{ $h->created_at->format('d M Y H:i') }}
                            @if($h->changedBy)
                                by {{ $h->changedBy->name }}
                            @endif
                        </small>
                    </li>
                    @empty
                    <li class="list-group-item text-muted">No history yet.</li>
                    @endforelse
                </ul>
            </div>

            {{-- Return / Refund (if any) --}}
            @if($order->returnRequest)
            <div class="card border-0 shadow-sm mb-4 border-warning" style="border-radius:16px;">
                <div class="card-header bg-warning bg-opacity-10 border-bottom-0 py-3 fw-bold">
                    <i class="bi bi-arrow-return-left me-2"></i>Return Request
                    <span class="badge bg-warning text-dark ms-2 shadow-sm">{{ ucfirst($order->returnRequest->status) }}</span>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Reason:</strong> {{ $order->returnRequest->reason->reason ?? $order->returnRequest->reason_text ?? '—' }}</p>
                    @if($order->returnRequest->description)
                        <p class="mb-1"><strong>Description:</strong> {{ $order->returnRequest->description }}</p>
                    @endif
                    @if($order->returnRequest->admin_notes)
                        <p class="mb-1"><strong>Admin Notes:</strong> {{ $order->returnRequest->admin_notes }}</p>
                    @endif
                    @if($order->refund)
                        <div class="alert alert-success mt-2 mb-0 py-2">
                            <i class="bi bi-check-circle me-1"></i>
                            Refund of {{ config('plantix.currency_symbol', 'Rs') }} {{ number_format($order->refund->amount, 2) }}
                            processed via {{ ucfirst($order->refund->method) }}
                            on {{ $order->refund->processed_at?->format('d M Y') ?? '—' }}.
                        </div>
                    @endif
                    <a href="{{ route('admin.returns.show', $order->returnRequest->id) }}"
                       class="btn btn-sm btn-outline-warning mt-2">View Full Return</a>
                </div>
            </div>
            @endif
        </div>

        {{-- Right column: details + actions --}}
        <div class="col-lg-4">

            {{-- Update Status --}}
            @if(!in_array($order->status, ['delivered','cancelled','rejected']))
            <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
                <div class="card-header bg-white border-bottom py-3 fw-bold text-success"><i class="bi bi-pencil-square me-2"></i>Update Status</div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small">New Status</label>
                            <select name="status" class="form-select form-select-sm" required>
                                @foreach(['pending','accepted','preparing','ready','driver_assigned','picked_up','delivered','rejected','cancelled'] as $s)
                                    <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $s)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Notes (optional)</label>
                            <textarea name="notes" class="form-control form-control-sm" rows="2"
                                      placeholder="Admin notes…"></textarea>
                        </div>
                        <button type="submit" class="btn btn-sm btn-success fw-bold rounded-pill shadow-sm w-100 mt-2 py-2">
                            <i class="bi bi-check2 me-1"></i>Update Status
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- Assign Driver --}}
            @if(in_array($order->status, ['ready','driver_assigned']))
            <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
                <div class="card-header bg-white border-bottom py-3 fw-bold text-success"><i class="bi bi-truck me-2"></i>Assign Driver</div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.orders.assign-driver', $order->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small">Driver ID</label>
                            <input type="number" name="driver_id" class="form-control form-control-sm"
                                   placeholder="User ID with role=driver"
                                   value="{{ $order->driver_id }}">
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline-success fw-bold rounded-pill shadow-sm w-100 mt-2 py-2">
                            <i class="bi bi-person-check me-1"></i>Assign Driver
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- Order Summary --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
                <div class="card-header bg-white border-bottom py-3 fw-bold text-success"><i class="bi bi-info-circle me-2"></i>Order Info</div>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Order #</span>
                        <strong>{{ $order->order_number }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Customer</span>
                        <span>{{ $order->user->name ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Vendor</span>
                        <span>{{ $order->vendor->name ?? '—' }}</span>
                    </li>
                    @if($order->driver)
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Driver</span>
                        <span>{{ $order->driver->name }}</span>
                    </li>
                    @endif
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Payment Method</span>
                        <span>{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Payment Status</span>
                        @php $payClass = ['paid'=>'success','pending'=>'warning','failed'=>'danger','refunded'=>'secondary']; @endphp
                        <span class="badge bg-{{ $payClass[$order->payment_status] ?? 'secondary' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </li>
                    @if($order->coupon)
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Coupon</span>
                        <span class="text-success">{{ $order->coupon->code }}</span>
                    </li>
                    @endif
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Placed</span>
                        <span>{{ $order->created_at->format('d M Y H:i') }}</span>
                    </li>
                </ul>
            </div>

            {{-- Delivery Address --}}
            @if($order->delivery_address)
            <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
                <div class="card-header bg-white border-bottom py-3 fw-bold text-success"><i class="bi bi-geo-alt me-2"></i>Delivery Address</div>
                <div class="card-body small">
                    {{ $order->delivery_address }}
                    @if($order->delivery_lat && $order->delivery_lng)
                        <div class="mt-1">
                            <a href="https://maps.google.com/?q={{ $order->delivery_lat }},{{ $order->delivery_lng }}"
                               target="_blank" class="btn btn-xs btn-outline-secondary btn-sm py-0">
                                <i class="bi bi-map me-1"></i>View on map
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
