@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-7">

            @if($errors->any())
                <div class="alert alert-danger mt-3">
                    @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
                </div>
            @endif

            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Stock: {{ $stock->product->name ?? '#' . $stock->id }}</h5>
                    <a href="{{ route('admin.stock.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back
                    </a>
                </div>
                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <label class="small text-muted">Product</label>
                            <p class="fw-semibold mb-0">{{ $stock->product->name ?? '—' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="small text-muted">Vendor</label>
                            <p class="fw-semibold mb-0">{{ $stock->vendor->name ?? '—' }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.stock.update', $stock->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                                       value="{{ old('quantity', $stock->quantity) }}" min="0" required>
                                @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold">Low Stock Threshold</label>
                                <input type="number" name="low_stock_threshold"
                                       class="form-control @error('low_stock_threshold') is-invalid @enderror"
                                       value="{{ old('low_stock_threshold', $stock->low_stock_threshold) }}" min="0">
                                @error('low_stock_threshold')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold">SKU</label>
                                <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror"
                                       value="{{ old('sku', $stock->sku) }}" maxlength="100">
                                @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Save Changes
                            </button>
                            <a href="{{ route('admin.stock.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>

                    <hr class="my-4">

                    {{-- Quick Adjust --}}
                    <h6 class="fw-semibold">Quick Stock Adjustment</h6>
                    <form method="POST" action="{{ route('admin.stock.adjust', $stock->id) }}" class="row g-2 align-items-end">
                        @csrf
                        <div class="col-sm-4">
                            <label class="form-label small">Adjustment Amount</label>
                            <input type="number" name="adjustment" class="form-control form-control-sm"
                                   placeholder="e.g. 10 or -5" required>
                        </div>
                        <div class="col-sm-5">
                            <label class="form-label small">Note (optional)</label>
                            <input type="text" name="note" class="form-control form-control-sm"
                                   placeholder="Reason...">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-warning btn-sm">Apply Adjustment</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
