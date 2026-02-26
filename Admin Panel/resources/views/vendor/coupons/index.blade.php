@extends('vendor.layouts.app')
@section('title', 'My Coupons')
@section('page-title', 'Discount Coupons')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0">Create and manage discount coupons for your store.</p>
    <a href="{{ route('vendor.coupons.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>New Coupon
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if ($coupons->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="bi bi-tag fs-1 d-block mb-2"></i>
                <p>No coupons yet. <a href="{{ route('vendor.coupons.create') }}">Create your first one.</a></p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Min. Order</th>
                            <th>Usage</th>
                            <th>Expires</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($coupons as $coupon)
                            <tr>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $coupon->code }}</code>
                                </td>
                                <td class="text-capitalize">{{ $coupon->type }}</td>
                                <td>
                                    @if ($coupon->type === 'percentage')
                                        {{ $coupon->value }}%
                                    @else
                                        {{ config('plantix.currency_symbol') }}{{ number_format($coupon->value, 2) }}
                                    @endif
                                </td>
                                <td>
                                    {{ $coupon->min_order
                                        ? config('plantix.currency_symbol') . number_format($coupon->min_order, 2)
                                        : '—' }}
                                </td>
                                <td>
                                    {{ $coupon->used_count }}
                                    @if ($coupon->usage_limit)
                                        / {{ $coupon->usage_limit }}
                                    @endif
                                </td>
                                <td>
                                    @if ($coupon->expires_at)
                                        <span class="{{ $coupon->expires_at->isPast() ? 'text-danger' : 'text-muted' }}">
                                            {{ $coupon->expires_at->format('d M Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted">No expiry</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($coupon->is_active && $coupon->isValid())
                                        <span class="badge bg-success">Active</span>
                                    @elseif (!$coupon->is_active)
                                        <span class="badge bg-secondary">Disabled</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Expired / Limit Reached</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        {{-- Toggle active --}}
                                        <form action="{{ route('vendor.coupons.toggle', $coupon->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $coupon->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                    title="{{ $coupon->is_active ? 'Disable' : 'Enable' }}">
                                                <i class="bi bi-{{ $coupon->is_active ? 'pause-circle' : 'play-circle' }}"></i>
                                            </button>
                                        </form>

                                        <a href="{{ route('vendor.coupons.edit', $coupon->id) }}"
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <form action="{{ route('vendor.coupons.destroy', $coupon->id) }}" method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Delete coupon {{ $coupon->code }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
