@extends('vendor.layouts.app')
@section('title', $coupon ? 'Edit Coupon' : 'New Coupon')
@section('page-title', $coupon ? 'Edit Coupon' : 'Create New Coupon')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-tag me-2 text-primary"></i>
                    {{ $coupon ? 'Edit Coupon: ' . $coupon->code : 'New Discount Coupon' }}
                </h6>
            </div>
            <div class="card-body">

                @if ($coupon)
                    <form action="{{ route('vendor.coupons.update', $coupon->id) }}" method="POST">
                        @csrf @method('PUT')
                @else
                    <form action="{{ route('vendor.coupons.store') }}" method="POST">
                        @csrf
                @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Coupon Code</label>
                        <input type="text" name="code" class="form-control text-uppercase @error('code') is-invalid @enderror"
                               value="{{ old('code', $coupon?->code) }}"
                               placeholder="Leave blank to auto-generate"
                               style="text-transform:uppercase;">
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Letters and numbers only. Max 50 characters.</div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Discount Type <span class="text-danger">*</span></label>
                            <select name="type" id="discountType" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="percentage" {{ old('type', $coupon?->type) === 'percentage' ? 'selected' : '' }}>
                                    Percentage (%)
                                </option>
                                <option value="fixed" {{ old('type', $coupon?->type) === 'fixed' ? 'selected' : '' }}>
                                    Fixed Amount ({{ config('plantix.currency_symbol') }})
                                </option>
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold" id="valueLabel">Value <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" id="valueSymbol">%</span>
                                <input type="number" step="0.01" name="value"
                                       class="form-control @error('value') is-invalid @enderror"
                                       value="{{ old('value', $coupon?->value) }}" min="0.01" required>
                                @error('value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Minimum Order Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ config('plantix.currency_symbol') }}</span>
                                <input type="number" step="0.01" name="min_order"
                                       class="form-control @error('min_order') is-invalid @enderror"
                                       value="{{ old('min_order', $coupon?->min_order) }}" min="0">
                            </div>
                            @error('min_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Leave blank for no minimum.</div>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Max Discount Cap</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ config('plantix.currency_symbol') }}</span>
                                <input type="number" step="0.01" name="max_discount"
                                       class="form-control @error('max_discount') is-invalid @enderror"
                                       value="{{ old('max_discount', $coupon?->max_discount) }}" min="0">
                            </div>
                            @error('max_discount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Cap the maximum discount amount (for % coupons).</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Start Date</label>
                            <input type="datetime-local" name="starts_at"
                                   class="form-control @error('starts_at') is-invalid @enderror"
                                   value="{{ old('starts_at', $coupon?->starts_at?->format('Y-m-d\TH:i')) }}">
                            @error('starts_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Expiry Date</label>
                            <input type="datetime-local" name="expires_at"
                                   class="form-control @error('expires_at') is-invalid @enderror"
                                   value="{{ old('expires_at', $coupon?->expires_at?->format('Y-m-d\TH:i')) }}">
                            @error('expires_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Usage Limit</label>
                        <input type="number" name="usage_limit"
                               class="form-control @error('usage_limit') is-invalid @enderror"
                               value="{{ old('usage_limit', $coupon?->usage_limit) }}"
                               min="1" placeholder="Leave blank for unlimited uses">
                        @error('usage_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive"
                                   {{ old('is_active', $coupon?->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isActive">Active</label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i>{{ $coupon ? 'Update Coupon' : 'Create Coupon' }}
                        </button>
                        <a href="{{ route('vendor.coupons.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
const typeSelect = document.getElementById('discountType');
const symbol     = document.getElementById('valueSymbol');

function updateSymbol() {
    symbol.textContent = typeSelect.value === 'percentage'
        ? '%'
        : '{{ config('plantix.currency_symbol') }}';
}
typeSelect.addEventListener('change', updateSymbol);
updateSymbol();
</script>
@endpush
